<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewMessageNotification;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $messages = Message::where(function($query) {
                $query->where('receiver_id', Auth::id())
                    ->where('receiver_type', Auth::getDefaultDriver())
                    ->orWhere('sender_id', Auth::id())
                    ->where('sender_type', Auth::getDefaultDriver());
            })
            ->when($request->has('after'), function($query) use ($request) {
                return $query->where('id', '>', $request->after);
            })
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

            return response()->json(['messages' => $messages]);
        }

        $messages = Message::where('receiver_id', Auth::id())
            ->where('receiver_type', Auth::getDefaultDriver())
            ->orWhere('sender_id', Auth::id())
            ->where('sender_type', Auth::getDefaultDriver())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('messages.index', compact('messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'receiver_type' => 'required|in:admin,member',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'sender_type' => Auth::getDefaultDriver(),
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message,
        ]);

        // メール通知の送信
        if ($request->receiver_type === 'member') {
            $receiver = \App\Models\User::find($request->receiver_id);
            Mail::to($receiver->email)->send(new NewMessageNotification($message));
        }

        return redirect()->back()->with('success', 'メッセージを送信しました。');
    }

    public function markAsRead(Message $message)
    {
        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('receiver_type', Auth::getDefaultDriver())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
