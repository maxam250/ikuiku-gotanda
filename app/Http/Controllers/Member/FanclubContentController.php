<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Fanclub;
use App\Models\FanclubContent;
use App\Models\FanclubContentCategory;
use App\Models\FanclubMembership;
use App\Models\FanclubMessage;
use Illuminate\Support\Facades\Auth;

class FanclubContentController extends Controller
{
    /** チャンネルごとの投稿一覧は 1 ページにつき 1 投稿（?page=） */
    private const POSTS_PER_PAGE = 1;

    /** 各投稿内の子ブロックは 1 ページにつき 1 件（?child_page=） */
    private const CHILD_BLOCKS_PER_PAGE = 1;

    public function show(Fanclub $fanclub)
    {
        $member = Auth::guard('member')->user();

        $membership = FanclubMembership::query()
            ->where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->first();

        $canViewPaid = $membership && $membership->status === 'active';

        $channels = FanclubContentCategory::query()
            ->where('cast_id', $fanclub->cast_id)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        $hasUncategorized = $fanclub->contents()
            ->visible()
            ->whereNull('category_id')
            ->exists();

        $requestedContentId = request()->query('content');
        $requestedChannelId = request()->query('channel');
        if ($requestedChannelId === null || $requestedChannelId === '') {
            $requestedChannelId = request()->query('category');
        }

        $selectedChannel = null;
        $viewingUncategorized = false;

        // 公開側から ?content=（fanclub_content id）で直接指定（カテゴリ未設定チャンネル用）
        if ($requestedContentId !== null && $requestedContentId !== '') {
            $target = FanclubContent::query()
                ->where('cast_id', $fanclub->cast_id)
                ->whereKey((int) $requestedContentId)
                ->first();

            if ($target && $this->contentIsVisibleForViewer($target, $canViewPaid)) {
                if ($target->category_id) {
                    $selectedChannel = $channels->firstWhere('id', $target->category_id);
                } else {
                    $viewingUncategorized = true;
                }
            }
        }

        if (! $viewingUncategorized) {
            if ($channels->isNotEmpty()) {
                if ($requestedChannelId !== null && $requestedChannelId !== '') {
                    $selectedChannel = $channels->firstWhere('id', (int) $requestedChannelId);
                }
                if (! $selectedChannel) {
                    $selectedChannel = $channels->first();
                }
            } elseif ($hasUncategorized) {
                $viewingUncategorized = true;
            }
        }

        // category_id = チャンネル（fanclub_contents_categories）への紐付け。クエリでは channel で指定する。
        // 子ブロックは別クエリで paginate（child_page）するためここでは読み込まない。
        $query = $fanclub->contents()
            ->with(['category'])
            ->visible()
            ->when(! $canViewPaid, function ($query) {
                $query->where('is_paid', false);
            });

        if ($viewingUncategorized) {
            $query->whereNull('category_id');
        } elseif ($selectedChannel) {
            $query->where('category_id', $selectedChannel->id);
        } else {
            $query->whereRaw('0 = 1');
        }

        // チャンネル内のみを対象に 1 ページ＝1 投稿（?channel=&page=）
        $contents = $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(self::POSTS_PER_PAGE, ['*'], 'page')
            ->onEachSide(1);

        $appendQuery = collect(request()->query())
            ->except(['page', 'category', 'child_page'])
            ->all();
        if ($viewingUncategorized) {
            if ($requestedContentId !== null && $requestedContentId !== '') {
                $appendQuery['content'] = (int) $requestedContentId;
            }
        } elseif ($selectedChannel) {
            $appendQuery['channel'] = $selectedChannel->id;
        }
        $contents->appends($appendQuery);

        $childPaginator = null;
        if ($contents->isNotEmpty()) {
            $content = $contents->first();
            $childPaginator = $content->children()
                ->visible()
                ->orderBy('sort')
                ->orderBy('id')
                ->with('items')
                ->paginate(self::CHILD_BLOCKS_PER_PAGE, ['*'], 'child_page')
                ->onEachSide(1);

            $childAppend = collect(request()->query())
                ->except(['child_page', 'category'])
                ->all();
            if ($viewingUncategorized) {
                if ($requestedContentId !== null && $requestedContentId !== '') {
                    $childAppend['content'] = (int) $requestedContentId;
                }
            } elseif ($selectedChannel) {
                $childAppend['channel'] = $selectedChannel->id;
            }
            $childAppend['page'] = $contents->currentPage();
            $childPaginator->appends($childAppend);
        }

        $childPrevTitle = null;
        $childNextTitle = null;
        if ($childPaginator && $contents->isNotEmpty()) {
            $content = $contents->first();
            $orderedTitles = $content->children()
                ->visible()
                ->orderBy('sort')
                ->orderBy('id')
                ->pluck('title');
            $idx = $childPaginator->currentPage() - 1;
            if ($idx > 0) {
                $childPrevTitle = self::formatChildNavTitle($orderedTitles[$idx - 1] ?? null);
            }
            if ($idx < $orderedTitles->count() - 1) {
                $childNextTitle = self::formatChildNavTitle($orderedTitles[$idx + 1] ?? null);
            }
        }

        if (request()->expectsJson()) {
            return response()->json([
                'fanclub' => $fanclub->load('cast'),
                'membership' => $membership,
                'can_view_paid' => $canViewPaid,
                'channels' => $channels,
                'selected_channel' => $selectedChannel,
                'contents' => $contents,
                'child_paginator' => $childPaginator,
            ]);
        }

        // メッセージ未読バッジ件数（入会中のみ）
        $fcMsgUnread = 0;
        if ($membership && $membership->status === 'active') {
            $memberMsgIds = FanclubMessage::where('fanclub_id', $fanclub->id)
                ->where('member_id', $member->id)
                ->whereNull('parent_id')
                ->pluck('id');
            $fcMsgUnread = FanclubMessage::whereIn('parent_id', $memberMsgIds)
                ->where('sender_type', 'cast')
                ->where('is_read', false)
                ->count();
        }

        return view('member.fanclubs.show', [
            'fanclub' => $fanclub->load('cast'),
            'contents' => $contents,
            'childPaginator' => $childPaginator,
            'membership' => $membership,
            'canViewPaid' => $canViewPaid,
            'channels' => $channels,
            'selectedChannel' => $selectedChannel,
            'hasUncategorized' => $hasUncategorized,
            'viewingUncategorized' => $viewingUncategorized,
            'childPrevTitle' => $childPrevTitle,
            'childNextTitle' => $childNextTitle,
            'fcMsgUnread' => $fcMsgUnread,
        ]);
    }

    private function contentIsVisibleForViewer(FanclubContent $content, bool $canViewPaid): bool
    {
        if ($content->published_at === null || $content->published_at->isFuture()) {
            return false;
        }

        if ($content->is_paid && ! $canViewPaid) {
            return false;
        }

        return true;
    }

    private static function formatChildNavTitle(?string $title): string
    {
        $t = $title !== null ? trim($title) : '';

        return $t !== '' ? $t : '（無題）';
    }
}

