<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\MessageItem;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ItemMessageToAdminNotification;

class MessageController extends Controller
{
    public function index()
    {
        $messages = MessageItem::where(function($query) {
            $query->where('sender_id', Auth::guard('item')->id())
                ->where('sender_type', 'item')
                ->orWhere(function($q) {
                    $q->where('receiver_id', Auth::guard('item')->id())
                        ->where('receiver_type', 'item');
                });
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return view('item.messages.index', compact('messages'));
    }

    public function show(MessageItem $message)
    {
        if (!$message->is_read && $message->receiver_type === 'item') {
            $message->update(['is_read' => true]);
        }

        return view('item.messages.show', compact('message'));
    }

    public function create()
    {
        return view('item.messages.create');
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
            $filename = 'item_' . Auth::guard('item')->id() . '_' . date('Ymd_His') . '.' . $ext;
            $filename = mb_strimwidth($filename, 0, 255);
            $imagePath = $file->storeAs('messages', $filename, 'public');
        }

        $message = MessageItem::create([
            'sender_id' => Auth::guard('item')->id(),
            'sender_type' => 'item',
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
                    ->send(new ItemMessageToAdminNotification($message));
            }
        } catch (\Exception $e) {
            \Log::error('キャストメッセージ通知メール送信エラー: ' . $e->getMessage());
        }

        return redirect()->route('item.messages.index')
            ->with('success', 'メッセージを送信しました。');
    }

    public function destroy(MessageItem $message)
    {
        if ($message->sender_type === 'item' && $message->sender_id === Auth::guard('item')->id()) {
            $message->delete();
            return redirect()->route('item.messages.index')
                ->with('success', 'メッセージを削除しました。');
        }

        return redirect()->route('item.messages.index')
            ->with('error', 'このメッセージは削除できません。');
    }
}
