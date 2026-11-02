<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Reservation;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\Equipment;
use App\Models\Costume;
use App\Models\Fanclub;
use App\Models\FanclubContent;
use App\Models\FanclubContentChild;
use App\Models\FanclubMembership;
use App\Models\FanclubMessage;
use App\Services\ExternalDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        // 外部DB対応
        $reservationModel = ExternalDatabaseService::getReservationModel();
        
        // 最近の予約（最新5件）
        $recentReservations = $reservationModel::where('item_id', $item->id)
            ->where('date', '>=', adjustTimeWithShift())
            ->orderBy('created_at', 'desc')
            ->get();

        // 今月の予約数
        $currentMonthReservations = $reservationModel::where('item_id', $item->id)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        // 前月の予約数
        $lastMonthReservations = $reservationModel::where('item_id', $item->id)
            ->whereYear('date', now()->subMonth()->year)
            ->whereMonth('date', now()->subMonth()->month)
            ->count();

        // 今月の売上
        $currentMonthSales = $reservationModel::where('item_id', $item->id)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('fee_total');

        // データ取得
        $items = Item::orderBy('item_name', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $equipments = Equipment::orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();

        // ファンクラブ概要
        $fanclub = Fanclub::where('cast_id', $item->id)->first();
        $fanclubChannelCount = 0;
        $fanclubPostCount = 0;
        $fanclubMembersCount = 0;
        $fanclubMembers = collect();
        $fanclubLatestMessages = collect();
        $fanclubUnreadCount = 0;

        if ($fanclub) {
            $fanclubChannelCount = FanclubContent::where('cast_id', $item->id)->count();
            $fanclubPostCount = FanclubContentChild::whereHas('parent', function ($query) use ($item) {
                $query->where('cast_id', $item->id);
            })->count();

            $fanclubMembersCount = FanclubMembership::where('fanclub_id', $fanclub->id)
                ->where('status', 'active')
                ->count();

            $fanclubMembers = FanclubMembership::with('member')
                ->where('fanclub_id', $fanclub->id)
                ->where('status', 'active')
                ->latest('started_at')
                ->limit(10)
                ->get();

            $fanclubLatestMessages = FanclubMessage::with('member')
                ->where('fanclub_id', $fanclub->id)
                ->whereNull('parent_id')
                ->where('sender_type', 'member')
                ->latest()
                ->limit(5)
                ->get();

            $fanclubUnreadCount = FanclubMessage::where('fanclub_id', $fanclub->id)
                ->whereNull('parent_id')
                ->where('sender_type', 'member')
                ->where('is_read', false)
                ->count();
        }

        return view('item.dashboard', compact(
            'item',
            'recentReservations',
            'currentMonthReservations',
            'lastMonthReservations',
            'currentMonthSales',
            'items',
            'courses',
            'course_fees',
            'options',
            'option_pays',
            'equipments',
            'costumes',
            'fanclub',
            'fanclubChannelCount',
            'fanclubPostCount',
            'fanclubMembersCount',
            'fanclubMembers',
            'fanclubLatestMessages',
            'fanclubUnreadCount'
        ));
    }



    public function updateRealtimeComment(Request $request, $id)
    {

        //　バリデータ
        $request->validate([
            'item_p_comment' => 'required|string',
        ]);

        // 項目の取得
        $item = Item::findOrFail($id);

        // リアルタイムコメント投稿時間
        $item_p_comment_date = date('Y-m-d H:i:s');
        $item->item_p_comment_date = $item_p_comment_date;

        $item->item_p_comment = $request->input('item_p_comment');
        $item->save();


        return redirect()->route('item.dashboard',['item' => $id])->with('success', 'アップデート成功');

        //var_dump($request);
    }

}
