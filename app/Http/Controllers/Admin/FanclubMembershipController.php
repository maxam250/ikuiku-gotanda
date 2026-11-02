<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FanclubMembership;
use App\Models\PaymentLog;
use App\Services\Fanclub\FanclubMembershipService;
use Illuminate\Http\Request;

class FanclubMembershipController extends Controller
{
    public function __construct(
        private readonly FanclubMembershipService $membershipService
    ) {
    }

    public function index(Request $request)
    {
        $memberships = FanclubMembership::query()
            ->with(['fanclub.cast', 'member'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn($q) => $q->where('payment_status', $request->string('payment_status')))
            ->when($request->filled('fanclub_id'), fn($q) => $q->where('fanclub_id', $request->integer('fanclub_id')))
            ->latest()
            ->paginate(50);

        if ($request->expectsJson()) {
            return response()->json($memberships);
        }

        return view('admin.fanclubs.memberships.index', compact('memberships'));
    }

    public function show(FanclubMembership $membership)
    {
        $membership->load(['fanclub.cast', 'member', 'paymentLogs' => fn($q) => $q->latest()->limit(20)]);

        return view('admin.fanclubs.memberships.show', compact('membership'));
    }

    public function update(Request $request, FanclubMembership $membership)
    {
        $validated = $request->validate([
            'status'         => ['required', 'in:active,paused,canceled'],
            'payment_status' => ['required', 'in:pending,paid,unpaid,refunded'],
            'admin_note'     => ['nullable', 'string', 'max:1000'],
        ]);

        $this->membershipService->changeStatus($membership, $validated['status']);
        $this->membershipService->updatePaymentStatus(
            $membership,
            $validated['payment_status'],
            $validated['admin_note'] ?? null
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'updated']);
        }

        return back()->with('success', 'サブスク状態を更新しました。');
    }

    /**
     * 警告送信
     */
    public function warn(Request $request, FanclubMembership $membership)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $this->membershipService->sendWarning($membership, $validated['reason']);

        return back()->with('success', '警告を送信しました（警告回数: ' . $membership->fresh()->warning_count . '）。');
    }

    /**
     * 期間停止
     */
    public function suspend(Request $request, FanclubMembership $membership)
    {
        $validated = $request->validate([
            'days'   => ['required', 'integer', 'min:1', 'max:365'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $this->membershipService->suspend($membership, $validated['days'], $validated['reason']);

        return back()->with('success', $validated['days'] . '日間の利用停止を設定しました。');
    }

    /**
     * 即時脱会
     */
    public function expel(Request $request, FanclubMembership $membership)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $this->membershipService->expel($membership, $validated['reason']);

        return back()->with('success', '即時脱会処理が完了しました。');
    }

    /**
     * 決済履歴一覧
     */
    public function paymentLogs(Request $request)
    {
        $logs = PaymentLog::query()
            ->with(['member', 'fanclub'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('fanclub_id'), fn($q) => $q->where('fanclub_id', $request->integer('fanclub_id')))
            ->latest()
            ->paginate(50);

        return view('admin.fanclubs.payment_logs.index', compact('logs'));
    }
}
