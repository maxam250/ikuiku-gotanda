<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Fanclub;
use App\Models\FanclubMessage;
use App\Models\FanclubMembership;
use App\Mail\FanclubMessageToCastMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FanclubMessageController extends Controller
{
    /** メッセージ専用ページ */
    public function index(Fanclub $fanclub)
    {
        $member = Auth::guard('member')->user();

        $membership = FanclubMembership::where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->first();

        abort_unless($membership, 403, '入会中のファンクラブのみ利用できます。');

        $fanclubMessages = FanclubMessage::with('replies')
            ->where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->paginate(15);

        $canSendMessageToday = !FanclubMessage::where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->where('sender_type', 'member')
            ->whereDate('created_at', today())
            ->exists();

        // キャストからの返信を既読に
        $memberMsgIds = FanclubMessage::where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->whereNull('parent_id')
            ->pluck('id');

        FanclubMessage::whereIn('parent_id', $memberMsgIds)
            ->where('sender_type', 'cast')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('member.fanclubs.messages', compact(
            'fanclub', 'fanclubMessages', 'canSendMessageToday', 'membership'
        ));
    }

    /** メッセージ送信 */
    public function store(Request $request, Fanclub $fanclub)
    {
        $member = Auth::guard('member')->user();

        $membership = FanclubMembership::where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->first();

        if (!$membership) {
            return back()->with('error', 'ファンクラブに入会中の方のみメッセージを送れます。');
        }

        // 1日1回制限
        $alreadySent = FanclubMessage::where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->where('sender_type', 'member')
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadySent) {
            return back()->with('error', 'メッセージは1日1回まで送信できます。');
        }

        $request->validate([
            'message' => 'required|string|max:2000',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        if (FanclubMessage::containsBannedWord($request->message)) {
            return back()->withInput()->with('error', '送信できない言葉が含まれています。');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'fcmsg_' . $member->id . '_' . date('Ymd_His') . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('fanclub_messages', $filename, 'public');
        }

        $msg = FanclubMessage::create([
            'fanclub_id'  => $fanclub->id,
            'member_id'   => $member->id,
            'parent_id'   => null,
            'sender_type' => 'member',
            'message'     => $request->message,
            'image_path'  => $imagePath,
        ]);

        try {
            $cast = $fanclub->cast;
            if ($cast && $cast->email) {
                Mail::to($cast->email)->send(new FanclubMessageToCastMail($msg->load('fanclub', 'member')));
            }
        } catch (\Exception $e) {
            \Log::error('ファンクラブメッセージ通知メール送信エラー: ' . $e->getMessage());
        }

        return redirect()->route('member.fanclubs.messages.index', $fanclub)
            ->with('success', 'メッセージを送信しました。');
    }

    /** メッセージ削除（ソフトデリート・自分の送信分のみ） */
    public function destroy(Fanclub $fanclub, FanclubMessage $fanclubMessage)
    {
        $member = Auth::guard('member')->user();

        abort_unless(
            $fanclubMessage->fanclub_id === $fanclub->id &&
            $fanclubMessage->member_id === $member->id &&
            $fanclubMessage->sender_type === 'member',
            403
        );

        $fanclubMessage->delete(); // SoftDeletes

        return redirect()->route('member.fanclubs.messages.index', $fanclub)
            ->with('success', 'メッセージを削除しました。');
    }
}
