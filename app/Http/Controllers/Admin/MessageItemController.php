<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageItem;
use App\Models\Item;
use App\Models\Admin;
use App\Models\MessageTemplate;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\Costume;
use App\Models\Reservation;
use App\Services\ExternalDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewMessageItemNotification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessageItemController extends Controller
{
    // キャスト一覧（最新メッセージ順）
    public function index()
    {
        $items = Item::select('items.*')
            ->selectSub(function($query) {
                $query->from('message_items')
                    ->selectRaw('MAX(message_items.created_at)')
                    ->whereRaw('message_items.sender_id = items.id OR message_items.receiver_id = items.id');
            }, 'last_message_at')
            ->selectSub(function($query) {
                $query->from('message_items')
                    ->selectRaw('COUNT(*)')
                    ->where('message_items.receiver_id', DB::raw('items.id'))
                    ->where('message_items.receiver_type', 'item')
                    ->where('message_items.is_read', false);
            }, 'unread_count')
            // 非公開キャストは出さない（ただし未読メッセージがある場合は読めるように残す）
            ->where(function ($q) {
                $q->where('items.item_active', 1)
                    ->orWhereExists(function ($sub) {
                        $sub->from('message_items')
                            ->whereColumn('message_items.sender_id', 'items.id')
                            ->where('message_items.sender_type', 'item')
                            ->where('message_items.receiver_type', 'admin')
                            ->where('message_items.is_read', false);
                    });
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('items.id')
            ->get();
        return view('admin.message_item.index', compact('items'));
    }

    // メッセージボード
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $messages = MessageItem::where(function($query) use ($item_id) {
            $query->where(function($q) use ($item_id) {
                $q->where('sender_type', 'admin')
                    ->where('receiver_id', $item_id)
                    ->where('receiver_type', 'item');
            })->orWhere(function($q) use ($item_id) {
                $q->where('sender_id', $item_id)
                    ->where('sender_type', 'item')
                    ->where('receiver_type', 'admin');
            });
        })
        ->latest()
        ->paginate(10);

        // 未読メッセージを既読にする（管理者全員の未読メッセージ）
        MessageItem::where('receiver_type', 'admin')
            ->where('sender_id', $item_id)
            ->where('sender_type', 'item')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // メッセージテンプレートを取得（キャスト用のみ）
        $templates = \App\Models\MessageTemplate::where(function($query) {
            $query->where('type', 'cast')
                  ->orWhere('type', 'common');
        })->get();

        // 予約情報を取得（特定のキャストの今日以降の予約）
        $reservations = ExternalDatabaseService::getCastFutureReservations($item_id);

        return view('admin.message_item.show', compact('item', 'messages', 'templates', 'reservations'));
    }

    // メッセージ送信
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:items,id',
            'message' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = 'item_' . Auth::guard('admin')->id() . '_' . date('Ymd_His') . '.' . $ext;
            $filename = mb_strimwidth($filename, 0, 255);
            $imagePath = $file->storeAs('messages', $filename, 'public');
        }

        // メール返信連携用スレッド識別子を生成（キャスト向け）
        $threadUuid = (string) \Illuminate\Support\Str::uuid();

        $message = MessageItem::create([
            'thread_uuid' => $threadUuid,
            'sender_id' => Auth::guard('admin')->id(),
            'sender_type' => 'admin',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => 'item',
            'message' => $request->message,
            'title' => $request->title,
            'image_path' => $imagePath,
        ]);
        // メール通知の送信
        $receiver = Item::find($request->receiver_id);
        try {
            // 暗号化されたメールアドレスを復号化（暗号化されていない場合も考慮）
            try {
                $decryptedEmail = Crypt::decryptString($receiver->getRawOriginal('email'));
            } catch (\Exception $decryptException) {
                // 暗号化されていない場合は、そのまま使用
                $decryptedEmail = $receiver->getRawOriginal('email');
                \Log::info('メールアドレスが暗号化されていません（Item ID: ' . $receiver->id . '）');
            }

            // shopテーブルからメールアドレスを取得（フォールバック付き）
            $shop = \App\Models\Shop::first();
            $bccEmail = $shop && $shop->shop_mail ? $shop->shop_mail : config('mail.shop_mail2');

            Mail::to($decryptedEmail)
                ->bcc($bccEmail)
                ->send(new NewMessageItemNotification($message));
        } catch (\Exception $e) {
            // メール送信エラーをログに記録
            \Log::error('メール送信エラー: ' . $e->getMessage());
            // メッセージは保存されているので、エラーは表示せずにリダイレクト
        }
        return redirect()->back()->with('success', 'メッセージを送信しました。');
    }


    public function destroy(MessageItem $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'メッセージを削除しました。');
    }

    public function markAsRead(MessageItem $message)
    {
        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function getUnreadCount()
    {
        $count = MessageItem::where('receiver_type', 'admin')
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * テンプレートを適用
     */
    public function applyTemplate(Request $request)
    {
        $template = MessageTemplate::findOrFail($request->template_id);

        // キャスト情報を取得
        $item = null;
        $reservation = null;

        if ($request->has('item_id')) {
            $item = Item::find($request->item_id);
        }

        if ($request->has('reservation_id')) {
            // 外部データベースから予約情報を取得
            $reservation = \App\Services\ExternalDatabaseService::getReservation($request->reservation_id);
        }

        // 変数を置換したコンテンツを取得
        $content = $template->replaceVariables($item, $reservation);

        return response()->json([
            'title' => $template->title,
            'content' => $content
        ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
