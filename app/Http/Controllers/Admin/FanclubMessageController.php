<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FanclubMessage;
use App\Models\FanclubBannedWord;
use App\Models\FanclubMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FanclubMessageController extends Controller
{
    /** メッセージ一覧 */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $isSuperAdmin = $admin && $admin->isSuperAdmin();

        $query = FanclubMessage::with(['fanclub.cast', 'member'])->whereNull('parent_id');

        // 一般管理者は削除済みを表示しない。スーパー管理者のみ削除済みも表示。
        if ($isSuperAdmin) {
            $query->withTrashed();
        }

        if ($request->filled('fanclub_id')) {
            $query->where('fanclub_id', $request->fanclub_id);
        }
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }
        if ($request->filled('warned')) {
            $query->where('is_warned', true);
        }
        if ($request->filled('hidden')) {
            $query->where('is_hidden', true);
        }
        if ($request->boolean('reply_only')) {
            $query->has('replies');
        }

        $messages = $query
            ->withCount('replies')
            ->withMax('replies', 'created_at')
            ->latest()
            ->paginate(100)
            ->withQueryString();

        $suspendedMap = FanclubMembership::whereNotNull('suspended_until')
            ->where('suspended_until', '>', now())
            ->get(['fanclub_id', 'member_id'])
            ->mapWithKeys(function ($row) {
                return [$row->fanclub_id . '_' . $row->member_id => true];
            });

        return view('admin.fanclub-messages.index', compact('messages', 'isSuperAdmin', 'suspendedMap'));
    }

    /** 警告ステータス更新（通常 / 警告 / 停止） */
    public function updateStatus(Request $request, FanclubMessage $fanclubMessage)
    {
        $request->validate([
            'status' => 'required|in:normal,warn,suspend',
        ]);

        $status = $request->input('status');

        if ($status === 'normal') {
            $fanclubMessage->update(['is_warned' => false]);
            FanclubMembership::where('fanclub_id', $fanclubMessage->fanclub_id)
                ->where('member_id', $fanclubMessage->member_id)
                ->update([
                    'suspended_until' => null,
                    'suspension_reason' => null,
                ]);
            return back()->with('success', 'ステータスを通常に更新しました。');
        }

        if ($status === 'warn') {
            $fanclubMessage->update(['is_warned' => true]);
            FanclubMembership::where('fanclub_id', $fanclubMessage->fanclub_id)
                ->where('member_id', $fanclubMessage->member_id)
                ->update([
                    'suspended_until' => null,
                    'suspension_reason' => null,
                ]);
            return back()->with('success', 'ステータスを警告に更新しました。');
        }

        $fanclubMessage->update(['is_warned' => false]);
        FanclubMembership::where('fanclub_id', $fanclubMessage->fanclub_id)
            ->where('member_id', $fanclubMessage->member_id)
            ->update([
                'suspended_until' => now()->addDays(30),
                'suspension_reason' => '管理画面ステータス更新（停止）',
            ]);
        return back()->with('success', 'ステータスを停止に更新しました。');
    }

    /** スレッド詳細 */
    public function show(FanclubMessage $fanclubMessage)
    {
        $fanclubMessage->load(['fanclub.cast', 'member', 'replies']);
        return view('admin.fanclub-messages.show', compact('fanclubMessage'));
    }

    /** 警告フラグ切り替え */
    public function warn(FanclubMessage $fanclubMessage)
    {
        $fanclubMessage->update(['is_warned' => !$fanclubMessage->is_warned]);
        return back()->with('success', '警告ステータスを更新しました。');
    }

    /** 表示/非表示切り替え */
    public function hide(Request $request, FanclubMessage $fanclubMessage)
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);
        $fanclubMessage->update([
            'is_hidden'  => !$fanclubMessage->is_hidden,
            'admin_note' => $request->admin_note,
        ]);
        return back()->with('success', '表示設定を更新しました。');
    }

    /** メッセージ削除（ソフトデリート） */
    public function destroy(FanclubMessage $fanclubMessage)
    {
        $fanclubMessage->delete();
        return redirect()->route('admin.fanclub-messages.index')->with('success', 'メッセージを削除しました。');
    }

    /** 会員の利用停止（ファンクラブメンバーシップをsuspend） */
    public function suspend(Request $request, FanclubMessage $fanclubMessage)
    {
        $request->validate([
            'suspension_reason' => 'required|string|max:500',
            'suspended_until'   => 'required|date|after:today',
        ]);

        FanclubMembership::where('fanclub_id', $fanclubMessage->fanclub_id)
            ->where('member_id', $fanclubMessage->member_id)
            ->update([
                'suspended_until'   => $request->suspended_until,
                'suspension_reason' => $request->suspension_reason,
            ]);

        $fanclubMessage->update(['admin_note' => '利用停止処置: ' . $request->suspension_reason]);

        return back()->with('success', '会員の利用を停止しました。');
    }

    // ── 禁止ワード管理 ──

    public function bannedWords()
    {
        $words = FanclubBannedWord::orderBy('word')->paginate(50);
        return view('admin.fanclub-banned-words.index', compact('words'));
    }

    public function storeBannedWord(Request $request)
    {
        $request->validate(['word' => 'required|string|max:100|unique:fanclub_banned_words,word']);
        FanclubBannedWord::create(['word' => $request->word]);
        return back()->with('success', '禁止ワードを追加しました。');
    }

    public function destroyBannedWord(FanclubBannedWord $fanclubBannedWord)
    {
        $fanclubBannedWord->delete();
        return back()->with('success', '削除しました。');
    }
}
