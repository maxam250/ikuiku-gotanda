<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Member;
use App\Models\Admin;
use App\Models\MessageTemplate;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\Costume;
use App\Models\Reservation;
use App\Models\GuestMessage;
use App\Services\ExternalDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewMessageNotification;
use Illuminate\Support\Facades\Crypt;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Member::select('members.*')
            ->selectSub(function($query) {
                $query->from('messages')
                    ->selectRaw('MAX(messages.created_at)')
                    ->whereRaw('messages.sender_id = members.id OR messages.receiver_id = members.id');
            }, 'last_message_at')
            ->selectSub(function($query) {
                $query->from('messages')
                    ->selectRaw('COUNT(*)')
                    ->where('messages.sender_id', DB::raw('members.id'))
                    ->where('messages.sender_type', 'member')
                    ->where('messages.receiver_type', 'admin')
                    ->where('messages.is_read', false);
            }, 'unread_count');

        // 暗号化されていないフィールドでの検索条件の適用
        if ($request->filled('member_id')) {
            $query->where('members.id', 'like', '%' . $request->member_id . '%');
        }

        if ($request->filled('member_no')) {
            $query->where('members.member_no', 'like', '%' . $request->member_no . '%');
        }

        // まずは暗号化されていないフィールドで絞り込み
        $members = $query->orderByDesc('last_message_at')
            ->orderByDesc('members.id')
            ->get();

        // 暗号化されたフィールドでの検索（名前、メール、電話番号）
        if ($request->filled('name') || $request->filled('email') || $request->filled('tel')) {
            $filteredMembers = collect();

            foreach ($members as $member) {
                $match = true;

                // 名前検索
                if ($request->filled('name')) {
                    $nameMatch = false;
                    try {
                        $decryptedName = Crypt::decryptString($member->getRawOriginal('name'));
                        if (strpos($decryptedName, $request->input('name')) !== false) {
                            $nameMatch = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        if (strpos($member->name, $request->input('name')) !== false) {
                            $nameMatch = true;
                        }
                    }

                    // ニックネーム検索
                    if (!$nameMatch && $member->nickname) {
                        if (strpos($member->nickname, $request->input('name')) !== false) {
                            $nameMatch = true;
                        }
                    }

                    if (!$nameMatch) {
                        $match = false;
                    }
                }

                // メール検索
                if ($match && $request->filled('email')) {
                    $emailMatch = false;
                    try {
                        $decryptedEmail = Crypt::decryptString($member->getRawOriginal('email'));
                        if (strpos($decryptedEmail, $request->input('email')) !== false) {
                            $emailMatch = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        if (strpos($member->email, $request->input('email')) !== false) {
                            $emailMatch = true;
                        }
                    }
                    if (!$emailMatch) {
                        $match = false;
                    }
                }

                // 電話番号検索
                if ($match && $request->filled('tel')) {
                    $telMatch = false;
                    try {
                        $decryptedTel = Crypt::decryptString($member->getRawOriginal('tel'));
                        if (strpos($decryptedTel, $request->input('tel')) !== false) {
                            $telMatch = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        if (strpos($member->tel, $request->input('tel')) !== false) {
                            $telMatch = true;
                        }
                    }
                    if (!$telMatch) {
                        $match = false;
                    }
                }

                if ($match) {
                    $filteredMembers->push($member);
                }
            }

            $members = $filteredMembers;
        }

        return view('admin.messages.index', compact('members'));
    }

    public function show($member_id)
    {
        $member = Member::findOrFail($member_id);

        // メンバー情報の復号化
        try {
            $member->email = Crypt::decryptString($member->getRawOriginal('email'));
            $member->name = Crypt::decryptString($member->getRawOriginal('name'));
        } catch (\Exception $e) {
            \Log::error('Member decryption error: ' . $e->getMessage());
        }

        $messages = Message::where(function($query) use ($member_id) {
            $query->where(function($q) use ($member_id) {
                $q->where('sender_type', 'admin')
                    ->where('receiver_id', $member_id)
                    ->where('receiver_type', 'member');
            })->orWhere(function($q) use ($member_id) {
                $q->where('sender_id', $member_id)
                    ->where('sender_type', 'member')
                    ->where('receiver_type', 'admin');
            });
        })
        ->latest()
        ->paginate(10);

        // 未読メッセージを既読にする（管理者全員の未読メッセージ）
        Message::where('receiver_type', 'admin')
            ->where('sender_id', $member_id)
            ->where('sender_type', 'member')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // メッセージテンプレートを取得（会員用のみ）
        $templates = MessageTemplate::where(function($query) {
            $query->where('type', 'member')
                  ->orWhere('type', 'common');
        })->get();

        // 予約情報を取得（特定の会員の今日以降の予約）
        $reservations = ExternalDatabaseService::getFutureReservations($member_id);

        $staffs = Staff::where('is_active', 1)->orderBy('sort')->orderBy('name')->get();

        return view('admin.messages.show', compact('member', 'messages', 'templates', 'reservations', 'staffs'));
    }

    public function create()
    {
        $members = Member::all();
        return view('admin.messages.create', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:members,id',
            'message' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = 'member_' . Auth::guard('admin')->id() . '_' . date('Ymd_His') . '.' . $ext;
            $filename = mb_strimwidth($filename, 0, 255);
            $imagePath = $file->storeAs('messages', $filename, 'public');
        }
        // メール返信連携用スレッド識別子。Reply-To は reply@{shop_domain} 固定／照合は Message-ID・In-Reply-To。
        $threadUuid = (string) \Illuminate\Support\Str::uuid();

        $message = Message::create([
            'thread_uuid' => $threadUuid,
            'sender_id' => Auth::guard('admin')->id(),
            'sender_type' => 'admin',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => 'member',
            'message' => $request->message,
            'title' => $request->title,
            'image_path' => $imagePath,
            'staff_id' => $request->staff_id ?: null,
        ]);

        // セッションのスタッフを更新
        if ($request->staff_id) {
            $staff = Staff::find($request->staff_id);
            if ($staff) {
                session(['selected_staff' => ['id' => $staff->id, 'name' => $staff->name, 'role' => $staff->role]]);
            }
        }

        // メール通知の送信
        $receiver = Member::find($request->receiver_id);
        try {
            // 暗号化されたメールアドレスを復号化（暗号化されていない場合も考慮）
            try {
                $decryptedEmail = Crypt::decryptString($receiver->getRawOriginal('email'));
            } catch (\Exception $decryptException) {
                // 暗号化されていない場合は、そのまま使用
                $decryptedEmail = $receiver->getRawOriginal('email');
                \Log::info('メールアドレスが暗号化されていません（Member ID: ' . $receiver->id . '）');
            }

            // shopテーブルからメールアドレスを取得（フォールバック付き）
            $shop = \App\Models\Shop::first();
            $bccEmail = $shop && $shop->shop_mail ? $shop->shop_mail : config('mail.shop_mail2');

            Mail::to($decryptedEmail)
                ->bcc($bccEmail)
                ->send(new NewMessageNotification($message));
        } catch (\Exception $e) {
            // メール送信エラーをログに記録
            \Log::error('メール送信エラー: ' . $e->getMessage());
            // メッセージは保存されているので、エラーは表示せずにリダイレクト
        }

        return redirect()->back()->with('success', 'メッセージを送信しました。');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'メッセージを削除しました。');
    }

    public function markAsRead(Message $message)
    {
        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function getUnreadCount()
    {
        $count = Message::where('receiver_type', 'admin')
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

        // 会員情報を取得
        $member = null;
        $reservation = null;

        if ($request->has('member_id')) {
            $member = Member::find($request->member_id);
        }

        if ($request->has('reservation_id')) {
            // 外部データベースから予約情報を取得
            $reservation = \App\Services\ExternalDatabaseService::getReservation($request->reservation_id);
        }

        // 変数を置換したコンテンツを取得
        $content = $template->replaceVariables($member, $reservation);

        return response()->json([
            'title' => $template->title,
            'content' => $content
        ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 非会員のメッセージ表示
     */
    public function showGuest($email)
    {
        // URLエンコードされたメールアドレスをデコード
        $decodedEmail = urldecode($email);

        // 非会員メッセージを取得
        // $messages = GuestMessage::findByEmail($decodedEmail)
        //     ->sortByDesc('created_at');


        // 非会員メッセージを取得
        $messageLists = GuestMessage::whereNotNull('email')->get();

        $messages = $messageLists->filter(function ($message) use ($decodedEmail) {
            try {
                return Crypt::decryptString($message->getRawOriginal('email')) === $decodedEmail;
            } catch (\Exception $e) {
                return false; // 復号失敗時は除外
            }
        })->sortByDesc('created_at');


        // 関連する予約情報を取得

        // 予約情報を取得（特定の会員の今日以降の予約）
        // $reservations2 = ExternalDatabaseService::getFutureReservations();


        $reservations = ExternalDatabaseService::getFutureReservations(0)
            ->filter(function ($reservation) use ($decodedEmail) {
                try {
                    $email = is_array($reservation) ? ($reservation['email'] ?? null) : $reservation->getRawOriginal('email');
                    if (!$email) return false;
                    return Crypt::decryptString($email) === $decodedEmail;
                } catch (\Exception $e) {
                    $email = is_array($reservation) ? ($reservation['email'] ?? null) : $reservation->getRawOriginal('email');
                    if (!$email) return false;
                    return $email === $decodedEmail;
                }
            });



        // 予約者名を取得（最初の予約から）
        $guestName = $reservations->first() ? $reservations->first()->name : $decodedEmail;

        // 予約情報を管理画面用にフォーマット
        $formattedReservations = ExternalDatabaseService::getGuestFutureReservations($decodedEmail);

        // メッセージテンプレートを取得
        $templates = MessageTemplate::all();

        return view('admin.messages.show_guest', compact('messages', 'decodedEmail', 'guestName', 'reservations', 'formattedReservations', 'templates'));
    }

    /**
     * 非会員へのメッセージ送信
     */
    public function storeGuest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = 'guest_' . date('Ymd_His') . '.' . $ext;
            $filename = mb_strimwidth($filename, 0, 255);
            $imagePath = $file->storeAs('messages', $filename, 'public');
        }

        // 関連する予約を検索
        $reservation = null;
        $reservations = Reservation::where('member_id', 0)->get();
        foreach ($reservations as $res) {
            try {
                $decryptedEmail = Crypt::decryptString($res->getRawOriginal('email'));
                if ($decryptedEmail === $request->email) {
                    $reservation = $res;
                    break;
                }
            } catch (\Exception $e) {
                if ($res->getRawOriginal('email') === $request->email) {
                    $reservation = $res;
                    break;
                }
            }
        }

        $message = GuestMessage::create([
            'email' => Crypt::encryptString($request->email),
            'sender_type' => 'admin',
            'message' => $request->message,
            'title' => $request->title,
            'image_path' => $imagePath,
            'reservation_id' => $reservation ? $reservation->id : null,
        ]);

        // メール通知の送信
        try {
            // shopテーブルからメールアドレスを取得（フォールバック付き）
            $shop = \App\Models\Shop::first();
            $fromEmail = $shop && $shop->shop_mail ? $shop->shop_mail : config('mail.from.address');

            // Mailgunを使用してメール送信
            Mail::to($request->email)
                ->send(new \App\Mail\GuestMessageNotification($message, $fromEmail));
        } catch (\Exception $e) {
            // メール送信エラーをログに記録
            \Log::error('非会員メール送信エラー: ' . $e->getMessage());
            // メッセージは保存されているので、エラーは表示せずにリダイレクト
        }

        return redirect()->back()->with('success', 'メッセージを送信しました。');
    }
}
