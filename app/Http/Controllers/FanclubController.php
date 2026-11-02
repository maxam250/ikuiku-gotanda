<?php

namespace App\Http\Controllers;

use App\Models\Fanclub;
use App\Models\FanclubContent;
use App\Models\FanclubMembership;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class FanclubController extends Controller
{
    public function index()
    {
        $member = Auth::guard('member')->user();

        $query = Fanclub::query()
            ->with(['cast.thumbnail'])
            ->withCount(['memberships as active_member_count' => function ($q) {
                $q->where('status', 'active');
            }])
            ->where('status', 'active')
            ->latest();

        $fanclubs = $query->paginate(12);

        $membershipByFanclubId = collect();
        if ($member && $fanclubs->isNotEmpty()) {
            $membershipByFanclubId = FanclubMembership::query()
                ->where('member_id', $member->id)
                ->whereIn('fanclub_id', $fanclubs->pluck('id'))
                ->get()
                ->keyBy('fanclub_id');
        }

        return view('fanclub.index', compact('fanclubs', 'member', 'membershipByFanclubId'));
    }

    public function show(Fanclub $fanclub)
    {
        $member = Auth::guard('member')->user();

        $membership = $member
            ? FanclubMembership::query()
                ->where('fanclub_id', $fanclub->id)
                ->where('member_id', $member->id)
                ->first()
            : null;

        $isMemberActive = $membership && $membership->status === 'active';

        // 公開済みコンテンツ（カテゴリ未設定のチャンネルも含む。会員側と同じ並び）
        $contents = $fanclub->contents()
            ->visible()
            ->with('category')
            ->withCount('children')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        $onlyFreeForPaging = ! $isMemberActive;
        $contentMemberPages = [];
        foreach ($contents as $content) {
            $contentMemberPages[$content->id] = $this->memberPageForContentInChannel($fanclub, $content, $onlyFreeForPaging);
        }

        $fanclub->load(['cast.thumbnail']);
        $fanclub->loadCount(['memberships as active_member_count' => function ($q) {
            $q->where('status', 'active');
        }]);

        return view('fanclub.show', [
            'fanclub' => $fanclub,
            'member' => $member,
            'membership' => $membership,
            'isMemberActive' => $isMemberActive,
            'contents' => $contents,
            'contentMemberPages' => $contentMemberPages,
        ]);
    }

    /**
     * 会員マイページの並び（チャンネル内・公開のみ・必要なら無料のみ）での投稿のページ番号。
     */
    private function memberPageForContentInChannel(Fanclub $fanclub, FanclubContent $content, bool $onlyFree): int
    {
        $q = $fanclub->contents()->visible();

        if ($content->category_id) {
            $q->where('category_id', $content->category_id);
        } else {
            $q->whereNull('category_id');
        }

        if ($onlyFree) {
            $q->where('is_paid', false);
        }

        $ids = $q->orderByDesc('published_at')
            ->orderByDesc('id')
            ->pluck('id')
            ->values();

        $idx = $ids->search($content->id);

        return $idx !== false ? (int) $idx + 1 : 1;
    }

    public function commerceLaw()
    {
        $setting = Setting::first();

        return view('fanclub.commerce_law', compact('setting'))
            ->with('forceNoindex', true);
    }
}
