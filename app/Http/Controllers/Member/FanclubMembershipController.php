<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Fanclub;
use App\Models\FanclubContent;
use App\Models\FanclubMembership;
use App\Models\Setting;
use App\Services\Fanclub\FanclubMembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FanclubMembershipController extends Controller
{
    public function __construct(
        private readonly FanclubMembershipService $membershipService
    ) {}

    public function index()
    {
        $member = Auth::guard("member")->user();
        $fanclubs = Fanclub::query()
            ->with(["cast", "memberships" => function ($q) use ($member) {
                $q->where("member_id", $member->id);
            }])
            ->where("status", "!=", "canceled")
            ->latest()
            ->paginate(24);
        if (request()->expectsJson()) {
            return response()->json($fanclubs);
        }
        return view("member.fanclubs.index", compact("fanclubs"));
    }

    public function joined()
    {
        $member = Auth::guard("member")->user();
        $memberships = FanclubMembership::query()
            ->with(["fanclub.cast"])
            ->where("member_id", $member->id)
            ->whereIn("status", ["active", "paused"])
            ->latest("updated_at")
            ->paginate(24);
        return view("member.fanclubs.joined", compact("memberships"));
    }

    public function contents()
    {
        $member = Auth::guard("member")->user();
        $joinedCastIds = FanclubMembership::query()
            ->where("member_id", $member->id)
            ->where("status", "active")
            ->with("fanclub:id,cast_id")
            ->get()
            ->pluck("fanclub.cast_id")
            ->filter()->unique()->values();
        $contents = FanclubContent::query()
            ->with(["cast", "category"])
            ->visible()
            ->when($joinedCastIds->isNotEmpty(), function ($q) use ($joinedCastIds) {
                $q->whereIn("cast_id", $joinedCastIds);
            }, function ($q) {
                $q->whereRaw("1 = 0");
            })
            ->latest("published_at")
            ->paginate(24);
        return view("member.fanclubs.contents", compact("contents"));
    }

    public function confirmSubscribe(Fanclub $fanclub)
    {
        $member = Auth::guard("member")->user();
        $existing = FanclubMembership::where("fanclub_id", $fanclub->id)
            ->where("member_id", $member->id)
            ->where("status", "active")
            ->first();
        if ($existing) {
            return redirect()->route("member.fanclubs.show", $fanclub)
                ->with("info", "すでに入会済みです。");
        }
        $setting           = Setting::first();
        $squareAppId       = config("fanclub.payments.square.application_id", "");
        $squareEnvironment = config("fanclub.payments.square.environment", "sandbox");
        $isDummy           = config("fanclub.payments.driver") === "dummy_square";
        return view("fanclub.subscribe_confirm", compact("fanclub", "setting", "squareAppId", "squareEnvironment", "isDummy"));
    }

    public function subscribe(Request $request, Fanclub $fanclub)
    {
        $member = Auth::guard("member")->user();
        $request->validate([
            "agreed_terms" => ["required", "accepted"],
        ]);

        try {
            // 無料ファンクラブは決済不要で即時入会
            if ($fanclub->price == 0) {
                $this->membershipService->joinFree($fanclub, $member);
                return redirect()->route("member.fanclubs.subscribe.complete", $fanclub)
                    ->with("success", "ファンクラブへの入会が完了しました。");
            }

            $redirectUrl = route("member.fanclubs.subscribe.complete", $fanclub);
            $result = $this->membershipService->initiateCheckout($fanclub, $member, $redirectUrl);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === "already_joined") {
                return redirect()->route("member.fanclubs.show", $fanclub)->with("info", "すでに入会済みです。");
            }
            Log::error("Fanclub checkout failed", ["member_id" => $member->id, "fanclub_id" => $fanclub->id, "error" => $e->getMessage()]);
            return back()->withErrors(["subscribe" => "決済処理の開始に失敗しました。しばらく経ってから再度お試しください。"]);
        } catch (\Throwable $e) {
            Log::error("Fanclub checkout unexpected error", ["member_id" => $member->id, "fanclub_id" => $fanclub->id, "error" => $e->getMessage()]);
            return back()->withErrors(["subscribe" => "決済処理の開始に失敗しました。しばらく経ってから再度お試しください。"]);
        }

        // Square チェックアウトページへリダイレクト
        if ($result["checkout_url"]) {
            return redirect()->away($result["checkout_url"]);
        }

        // Dummy モード: 即時完了のため完了ページへ直接遷移
        return redirect()->route("member.fanclubs.subscribe.complete", $fanclub)
            ->with("success", "ファンクラブへの入会手続きが完了しました。");
    }

    public function subscribeComplete(Fanclub $fanclub)
    {
        return view("member.fanclubs.subscribe_complete", compact("fanclub"));
    }

    public function updateStatus(Request $request, Fanclub $fanclub)
    {
        $member = Auth::guard("member")->user();
        $validated = $request->validate(["status" => ["required", "in:active,paused,canceled"]]);
        $membership = FanclubMembership::where("fanclub_id", $fanclub->id)
            ->where("member_id", $member->id)->firstOrFail();
        try {
            $this->membershipService->changeStatus($membership, $validated["status"]);
        } catch (\Throwable $e) {
            Log::error("Fanclub status change failed", ["membership_id" => $membership->id, "error" => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(["message" => "状態変更に失敗しました。"], 422);
            }
            return back()->withErrors(["status" => "状態変更に失敗しました。"]);
        }
        if ($request->expectsJson()) {
            return response()->json(["message" => "status updated"]);
        }
        $msg = $validated["status"] === "canceled" ? "退会処理が完了しました。" : "会員ステータスを更新しました。";
        return redirect()->route("member.fanclubs.show", $fanclub)->with("success", $msg);
    }

    public function confirmWithdraw(Fanclub $fanclub)
    {
        $member = Auth::guard("member")->user();
        $membership = FanclubMembership::where("fanclub_id", $fanclub->id)
            ->where("member_id", $member->id)->where("status", "active")->firstOrFail();
        $setting = Setting::first();
        return view("fanclub.withdraw_confirm", compact("fanclub", "membership", "setting"));
    }
}
