<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\MemberMessageToAdminNotification;

class MessageController extends Controller
{
    public function index()
    {
        // 未読メッセージを既読にする（管理者からのメッセージのみ）
        Message::where('receiver_id', Auth::guard('member')->id())
            ->where('receiver_type', 'member')
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $messages = Message::where(function($query) {
            $query->where('sender_id', Auth::guard('member')->id())
                ->where('sender_type', 'member')
                ->orWhere(function($q) {
                    $q->where('receiver_id', Auth::guard('member')->id())
                        ->where('receiver_type', 'member');
                });
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return view('member.messages.index', compact('messages'));
    }

    public function show(Message $message)
    {
        // メッセージの受信者が現在のmemberで、かつ未読の場合、既読にする
        if (!$message->is_read &&
            $message->receiver_type === 'member' &&
            $message->receiver_id === Auth::guard('member')->id()) {
            $message->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return view('member.messages.show', compact('message'));
    }

    public function create()
    {
        return view('member.messages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = 'member_' . Auth::guard('member')->id() . '_' . date('Ymd_His') . '.' . $ext;
            $filename = mb_strimwidth($filename, 0, 255);
            $imagePath = $file->storeAs('messages', $filename, 'public');
        }

        $message = Message::create([
            'sender_id' => Auth::guard('member')->id(),
            'sender_type' => 'member',
            'receiver_id' => 1, // 管理者のID
            'receiver_type' => 'admin',
            'title' => $request->title,
            'message' => $request->message,
            'image_path' => $imagePath,
            'is_read' => false,
        ]);

        // 管理者へのメール通知
        try {
            $admins = Admin::all();
            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->bcc(config('mail.shop_mail2'))
                    ->send(new MemberMessageToAdminNotification($message));
            }
        } catch (\Exception $e) {
            \Log::error('会員メッセージ通知メール送信エラー: ' . $e->getMessage());
        }

        return redirect()->route('member.messages.index')
            ->with('success', 'メッセージを送信しました。');
    }

    public function destroy(Message $message)
    {
        if ($message->sender_type === 'member' && $message->sender_id === Auth::guard('member')->id()) {
            $message->delete();
            return redirect()->route('member.messages.index')
                ->with('success', 'メッセージを削除しました。');
        }

        return redirect()->route('member.messages.index')
            ->with('error', 'このメッセージは削除できません。');
    }
}
