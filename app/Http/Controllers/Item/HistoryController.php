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
use App\Services\ExternalDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        // 外部DB対応
        $reservationModel = ExternalDatabaseService::getReservationModel();
        
        // 検索条件
        $query = $reservationModel::where('item_id', $item->id);

        // 日付範囲検索
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // ステータス検索
        if ($request->filled('flag')) {
            $query->where('flag', $request->flag);
        }

        // 予約履歴を取得（ページネーション付き）
        $reservations = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // データ取得
        $items = Item::orderBy('item_name', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $equipments = Equipment::orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();

        return view('item.history', compact(
            'item',
            'reservations',
            'items',
            'courses',
            'course_fees',
            'options',
            'option_pays',
            'equipments',
            'costumes'
        ));
    }
} 