<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Reservation;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\OptionCate;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\CourseCate;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\shop;
use App\Models\Message;
use App\Models\MessageItem;
use App\Models\Schedule;
use App\Models\Member;
use App\Models\AdminNotification;
use App\Models\SharedMemo;
use App\Models\Equipment;
use App\Models\Staff;
use App\Services\ExternalDatabaseService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // 新着予約
        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = Option::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $reservations = Reservation::with(['item', 'course', 'staff'])
            ->whereDate('date', '>=', $today)
            ->whereDate('date', '<=', $tomorrow)
            ->orderBy('date', 'asc')
            ->orderBy('time_start', 'asc')
            ->get();

        $todayReservations = $reservations->filter(function ($reservation) use ($today) {
            return optional($reservation->date)->isSameDay($today);
        })->values();

        $tomorrowReservations = $reservations->filter(function ($reservation) use ($tomorrow) {
            return optional($reservation->date)->isSameDay($tomorrow);
        })->values();

        // 新着通知データを取得
        $notifications = $this->getNotifications();

        // 共有メモ（直近10件）
        $sharedMemos = SharedMemo::with(['category', 'staff'])
            ->orderBy('created_at', 'desc')
            ->orderBy('memo_date', 'desc')
            ->limit(10)
            ->get();

        $equipments = Equipment::where('active', 1)->orderBy('sort', 'asc')->get();
        $staffs = Staff::orderBy('id', 'asc')->get();

        return view('admin.dashboard', compact(
            'items',
            'options',
            'option_pays',
            'courses',
            'course_fees',
            'equipments',
            'staffs',
            'reservations',
            'todayReservations',
            'tomorrowReservations',
            'today',
            'tomorrow',
            'notifications',
            'sharedMemos'
        ));
    }

    /**
     * ダッシュボード上段用の簡易アクセスログサマリー（直近7日間）。
     * access_logs テーブルが無い場合も安全に空データを返す。
     */
    private function getAccessLogSummary(): array
    {
        $empty = [
            'available'  => false,
            'from'       => null,
            'to'         => null,
            'totalPv'    => 0,
            'totalUu'    => 0,
            'directPv'   => 0,
            'labels'     => [],
            'pvSeries'   => [],
            'uuSeries'   => [],
            'daily'      => [],
            'referers'   => [],
        ];

        try {
            $from = Carbon::today()->subDays(6)->startOfDay();
            $to   = Carbon::today()->endOfDay();

            $base = DB::table('access_logs')->whereBetween('created_at', [$from, $to]);

            $daily = (clone $base)
                ->selectRaw('DATE(created_at) as d, COUNT(*) as pv, COUNT(DISTINCT visitor_id) as uu')
                ->groupBy('d')
                ->orderBy('d')
                ->get()
                ->keyBy('d');

            $labels = [];
            $pvSeries = [];
            $uuSeries = [];
            $rows = [];
            $cursor = $from->copy();
            while ($cursor->lte($to)) {
                $key = $cursor->toDateString();
                $pv = (int) ($daily[$key]->pv ?? 0);
                $uu = (int) ($daily[$key]->uu ?? 0);
                $labels[]   = $cursor->format('n/j');
                $pvSeries[] = $pv;
                $uuSeries[] = $uu;
                $rows[] = [
                    'label' => $cursor->format('n/j') . '(' . $cursor->isoFormat('ddd') . ')',
                    'pv'    => $pv,
                    'uu'    => $uu,
                ];
                $cursor->addDay();
            }

            $totalPv = (clone $base)->count();
            $totalUu = (clone $base)->distinct()->count('visitor_id');
            $directPv = (clone $base)->whereNull('referer_host')->count();

            $referers = (clone $base)
                ->whereNotNull('referer_host')
                ->selectRaw('referer_host, COUNT(*) as pv, COUNT(DISTINCT visitor_id) as uu')
                ->groupBy('referer_host')
                ->orderByDesc('pv')
                ->limit(5)
                ->get();

            return [
                'available'  => true,
                'from'       => $from,
                'to'         => $to,
                'totalPv'    => $totalPv,
                'totalUu'    => $totalUu,
                'directPv'   => $directPv,
                'labels'     => $labels,
                'pvSeries'   => $pvSeries,
                'uuSeries'   => $uuSeries,
                'daily'      => $rows,
                'referers'   => $referers,
            ];
        } catch (\Throwable $e) {
            return $empty;
        }
    }

    /**
     * 通知を既読にする
     */
    public function markNotificationAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');
        $notificationType = $request->input('notification_type');

        if ($notificationType === 'member_message' || $notificationType === 'item_message') {
            // メッセージの場合は、該当するメッセージを既読にする
            if ($notificationType === 'member_message') {
                $message = Message::where('sender_id', $notificationId)
                    ->where('receiver_type', 'admin')
                    ->where('is_read', false)
                    ->first();
            } else {
                $message = MessageItem::where('sender_id', $notificationId)
                    ->where('receiver_type', 'admin')
                    ->where('is_read', false)
                    ->first();
            }

            if ($message) {
                $message->update(['is_read' => true, 'read_at' => now()]);
                return response()->json(['success' => true]);
            }
        } else {
            // その他の通知の場合は、AdminNotificationテーブルから削除
            $notification = AdminNotification::find($notificationId);
            if ($notification) {
                $notification->delete();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * 新着通知データを取得
     */
    private function getNotifications()
    {
        // 既存の通知テーブルから未読の通知を取得
        $notifications = AdminNotification::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($notification) {
                return [
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'sender' => $notification->sender,
                    'created_at' => $notification->created_at,
                    'id' => $notification->id,
                    'color' => $notification->color
                ];
            })
            ->toArray();

        // 通知が少ない場合は、リアルタイムで通知を生成
        if (count($notifications) < 10) {
            $realtimeNotifications = $this->getRealtimeNotifications();
            $notifications = array_merge($notifications, $realtimeNotifications);
        }

        // 作成日時でソート
        usort($notifications, function($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });

        return array_slice($notifications, 0, 20); // 最大20件まで表示
    }

    /**
     * リアルタイム通知を取得
     */
    private function getRealtimeNotifications()
    {
        $notifications = [];

        // 1. 新規会員からのメッセージ（未読）
        try {
            $memberMessages = Message::where('receiver_type', 'admin')
                ->where('is_read', false)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($memberMessages as $message) {
                try {
                    $senderName = '会員';
                    // sender_typeが'member'の場合はMemberモデルを直接取得
                    if ($message->sender_type === 'member' && $message->sender_id) {
                        $member = \App\Models\Member::find($message->sender_id);
                        if ($member) {
                            $senderName = $member->name ?? '会員';
                        }
                    } elseif ($message->sender) {
                        $senderName = $message->sender->name ?? '会員';
                    }

                    $notifications[] = [
                        'type' => 'member_message',
                        'title' => '新規会員からのメッセージ',
                        'message' => $message->title ?: Str::limit($message->message, 50),
                        'sender' => $senderName,
                        'created_at' => $message->created_at,
                        'id' => $message->sender_id, // receiver_idではなくsender_idを使用
                        'color' => 'blue'
                    ];
                } catch (\Exception $e) {
                    // エラーが発生した場合はスキップ
                    continue;
                }
            }
        } catch (\Exception $e) {
            // エラーが発生した場合はスキップ
        }

        // 2. キャストからのメッセージ（未読）
        try {
            $itemMessages = MessageItem::where('receiver_type', 'admin')
                ->where('is_read', false)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($itemMessages as $message) {
                try {
                    $senderName = 'キャスト';
                    // sender_typeが'item'の場合はItemモデルを直接取得
                    if ($message->sender_type === 'item' && $message->sender_id) {
                        $item = \App\Models\Item::find($message->sender_id);
                        if ($item) {
                            $senderName = $item->item_name ?? 'キャスト';
                        }
                    } elseif ($message->sender) {
                        $senderName = $message->sender->item_name ?? 'キャスト';
                    }

                    $notifications[] = [
                        'type' => 'item_message',
                        'title' => 'キャストからのメッセージ',
                        'message' => $message->title ?: Str::limit($message->message, 50),
                        'sender' => $senderName,
                        'created_at' => $message->created_at,
                        'id' => $message->sender_id, // receiver_idではなくsender_idを使用
                        'color' => 'purple'
                    ];
                } catch (\Exception $e) {
                    // エラーが発生した場合はスキップ
                    continue;
                }
            }
        } catch (\Exception $e) {
            // エラーが発生した場合はスキップ
        }

        // 3. 出勤リクエスト（申請中）
        try {
            $attendanceRequests = Schedule::whereIn('flag', [1, 2, 3, 4])
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->with('item')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($attendanceRequests as $schedule) {
                try {
                    $flagText = '';
                    switch ($schedule->flag) {
                        case 1:
                            $flagText = '出勤申請';
                            break;
                        case 2:
                            $flagText = '削除申請';
                            break;
                        case 3:
                            $flagText = '変更申請';
                            break;
                        case 4:
                            $flagText = '削除申請';
                            break;
                    }

                    $itemName = $schedule->item->item_name ?? 'キャスト';
                    $notifications[] = [
                        'type' => 'attendance_request',
                        'title' => $flagText,
                        'message' => $itemName . ' - ' . $schedule->date,
                        'sender' => $itemName,
                        'created_at' => $schedule->created_at,
                        'id' => $schedule->id,
                        'color' => 'orange'
                    ];
                } catch (\Exception $e) {
                    // エラーが発生した場合はスキップ
                    continue;
                }
            }
        } catch (\Exception $e) {
            // エラーが発生した場合はスキップ
        }

        // 4. 新規予約（過去7日間）
        try {
            // 外部DBから予約データを取得
            $reservationModel = ExternalDatabaseService::getReservationModel();
            $newReservations = $reservationModel::where('shop_id' ,'=', 'kirari')
                ->orderBy('created_at', 'desc')
                ->get();
            // 新着7日間の設定は不要
            // where('created_at', '>=', Carbon::now()->subDays(37))

            foreach ($newReservations as $reservation) {
                try {
                    $notifications[] = [
                        'type' => 'new_reservation',
                        'title' => '新規予約',
                        'message' => $reservation->name . ' - ' . $reservation->date,
                        'sender' => $reservation->name,
                        'created_at' => $reservation->created_at,
                        'id' => $reservation->id,
                        'color' => 'green'
                    ];
                } catch (\Exception $e) {
                    // エラーが発生した場合はスキップ
                    continue;
                }
            }
        } catch (\Exception $e) {
            // エラーが発生した場合はスキップ
        }

        return $notifications;
    }
}
