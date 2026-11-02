<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Fanclub;
use App\Models\FanclubMessage;
use App\Mail\FanclubMessageToMemberMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FanclubMessageController extends Controller
{
    /** 会員メッセージ一覧（日付・nickname・本文） */
    public function memberList()
    {
        $cast = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->first();

        if (!$fanclub) {
            return view('item.fanclubs.messages.member-list', ['messages' => collect(), 'fanclub' => null]);
        }

        $messages = FanclubMessage::with('member')
            ->withCount('replies')
            ->where('fanclub_id', $fanclub->id)
            ->whereNull('parent_id')
            ->where('sender_type', 'member')
            ->latest()
            ->paginate(30);

        return view('item.fanclubs.messages.member-list', compact('messages', 'fanclub'));
    }

    /** 受信メッセージ一覧（会員ごとの最新メッセージ） */
    public function index()
    {
        $cast   = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->first();

        if (!$fanclub) {
            return view('item.fanclubs.messages.index', ['threads' => collect(), 'fanclub' => null, 'unreadCount' => 0]);
        }

        $unreadCount = FanclubMessage::where('fanclub_id', $fanclub->id)
            ->whereNull('parent_id')
            ->where('sender_type', 'member')
            ->where('is_read', false)
            ->count();

        // 各会員の最新メッセージ（スレッド一覧）
        $threads = FanclubMessage::with(['member'])
            ->where('fanclub_id', $fanclub->id)
            ->whereNull('parent_id')
            ->latest()
            ->paginate(20);

        // 未読を既読に
        FanclubMessage::where('fanclub_id', $fanclub->id)
            ->where('sender_type', 'member')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('item.fanclubs.messages.index', compact('threads', 'fanclub', 'unreadCount'));
    }

    /** スレッド詳細 */
    public function show(FanclubMessage $fanclubMessage)
    {
        $cast    = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();

        abort_unless($fanclubMessage->fanclub_id === $fanclub->id, 403);

        // このメッセージの会員とのやり取り全件
        $thread = FanclubMessage::with(['replies'])
            ->where('fanclub_id', $fanclub->id)
            ->where('member_id', $fanclubMessage->member_id)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        // 未読を既読
        FanclubMessage::where('fanclub_id', $fanclub->id)
            ->where('member_id', $fanclubMessage->member_id)
            ->where('sender_type', 'member')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('item.fanclubs.messages.show', compact('thread', 'fanclub', 'fanclubMessage'));
    }

    /** キャストからの返信 */
    public function reply(Request $request, FanclubMessage $fanclubMessage)
    {
        $cast    = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();

        abort_unless($fanclubMessage->fanclub_id === $fanclub->id, 403);

        // 返信は会員ごとに1日1回まで
        $alreadyRepliedToday = FanclubMessage::where('fanclub_id', $fanclub->id)
            ->where('member_id', $fanclubMessage->member_id)
            ->where('sender_type', 'cast')
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyRepliedToday) {
            return back()->with('error', '同一会員への返信は1日1回までです。');
        }

        $request->validate([
            'message' => 'required|string|max:2000',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        // 禁止ワードチェック
        if (FanclubMessage::containsBannedWord($request->message)) {
            return back()->withInput()->with('error', '送信できない言葉が含まれています。');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'fcmsg_cast_' . $cast->id . '_' . date('Ymd_His') . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('fanclub_messages', $filename, 'public');
        }

        $reply = FanclubMessage::create([
            'fanclub_id'  => $fanclub->id,
            'member_id'   => $fanclubMessage->member_id,
            'parent_id'   => $fanclubMessage->id,
            'sender_type' => 'cast',
            'message'     => $request->message,
            'image_path'  => $imagePath,
        ]);

        // 会員へメール通知
        try {
            $member = $fanclubMessage->member;
            if ($member && $member->email) {
                Mail::to($member->email)->send(
                    new FanclubMessageToMemberMail($reply->load('fanclub.cast', 'member'))
                );
            }
        } catch (\Exception $e) {
            \Log::error('ファンクラブ返信通知メール送信エラー: ' . $e->getMessage());
        }

        return back()->with('success', '返信しました。');
    }

    /** キャスト返信の削除（ソフトデリート） */
    public function destroy(FanclubMessage $fanclubMessage)
    {
        $cast = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();

        abort_unless(
            $fanclubMessage->fanclub_id === $fanclub->id &&
            $fanclubMessage->sender_type === 'cast',
            403
        );

        $fanclubMessage->delete();

        return back()->with('success', '返信メッセージを削除しました。');
    }
}
