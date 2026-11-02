<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Member;
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
use App\Models\Equipment;
use App\Models\Costume;
use App\Models\FanclubMessage;
use App\Services\ExternalDatabaseService;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // 会員情報
        $memberId = Auth::id();
        $member = Member::findOrFail($memberId);
        // $member->terms = $member->updateed_at ;

        // 有効期限の設定
        // 現在の日付から1年後の日付を計算
        $terms = Carbon::parse($member->updated_at)->addYear();
        // termsを更新

        //
        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = Option::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::where('active' , 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::orderBy('sort', 'asc')->get();

        // デバッグ情報を追加
        $externalEnabled = ExternalDatabaseService::isExternalEnabled();
        $externalConnectionTest = ExternalDatabaseService::testExternalConnection();

        // 外部DBサービスを使用して予約情報を取得（未来の予約のみ）
        $reservations = ExternalDatabaseService::getReservations([
            'member_id' => $memberId,
            'date' => ['>=', now()->format('Y-m-d')]
        ]);

        // 各予約オブジェクトに対してJSONデコードを行う
        $reservations = $reservations->map(function ($reservation) {
            $reservation->option = json_decode($reservation->option, true);
            $reservation->option_pay = json_decode($reservation->option_pay, true);
            return $reservation;
        });

        $equipments = Equipment::all();
        // 未読メッセージ数を取得（管理者からのメッセージのみ）
        $unreadMessagesCount = Message::where('receiver_id', $memberId)
            ->where('receiver_type', 'member')
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();

        // ファンクラブ返信の未読（キャスト -> 会員）
        $unreadFanclubReplyCount = FanclubMessage::with(['fanclub'])
            ->where('member_id', $memberId)
            ->where('sender_type', 'cast')
            ->whereNotNull('parent_id')
            ->where('is_read', false)
            ->count();

        $unreadFanclubReplies = FanclubMessage::with(['fanclub', 'parent'])
            ->where('member_id', $memberId)
            ->where('sender_type', 'cast')
            ->whereNotNull('parent_id')
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();

        // デバッグ情報をログに出力
        // \Log::info('Dashboard Debug Info', [
        //     'external_enabled' => $externalEnabled,
        //     'external_connection_test' => $externalConnectionTest,
        //     'member_id' => $memberId,
        //     'reservations_count' => $reservations->count(),
        //     'reservations' => $reservations->toArray(),
        //     'unread_messages_count' => $unreadMessagesCount
        // ]);

        return view('member.dashboard', compact(
            'items',
            'options',
            'option_pays',
            'courses',
            'course_fees',
            'reservations',
            'member',
            'terms',
            'unreadMessagesCount',
            'equipments',
            'costumes',
            'unreadFanclubReplyCount',
            'unreadFanclubReplies'
        ));
    }
}
