<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Schedule;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Option;
use App\Models\OptionCate;
use App\Models\OptionPay;
use App\Models\Reservation;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ScheduleController extends Controller
{

    public function index (Request $request, $date = null){

        // URLに日付が指定されていればその日付を、なければsettingsのschedule_switch_hourで調整した当日を使用
        $adjustedDate = $date
            ? Carbon::parse($date)
            : Carbon::parse(adjustTimeWithShift());

        // $dates = getWeekDates();
        // $schedule = Schedule::with('reservations')->first();
        // dd($schedule->toArray());

        $schedules = Schedule::whereDate('date', $adjustedDate)
                            ->whereNotIn('flag', [1, 4])    // 出勤申請中と削除申請中は非表示
                            ->whereHas('item')               // itemが存在しないスケジュールを除外
                            ->with([
                                'item' => function ($q) {
                                    $q->with([
                                        'category',
                                        'fanclub'    => fn($q2) => $q2->where('status', 'active'),
                                        'myoptions'  => fn($q2) => $q2->where('active', 1),
                                        'mycostumes' => fn($q2) => $q2->where('active', 1),
                                    ]);
                                },
                                'itemImage',
                            ]) // eager load
                            // ->where('flag', '0') // 申請中外
                            ->when($request->filled('category_id'), function ($q) use ($request) {
                                $q->whereHas('item', fn($q) => $q->where('item_category_id', $request->category_id));
                            })
                            ->when($request->filled('name'), function ($q) use ($request) {
                                $q->whereHas('item', fn($q2) => $q2->where('item_name', 'LIKE', '%' . $request->name . '%'));
                            })
                            ->when($request->filled('item_category_ids'), function ($q) use ($request) {
                                $ids = array_map('intval', (array) $request->item_category_ids);
                                $q->whereHas('item', fn($q2) => $q2->whereIn('item_category_id', $ids));
                            })
                            ->when($request->filled('option_cates'), function ($q) use ($request) {
                                $cates = array_map('intval', (array) $request->option_cates);
                                foreach ($cates as $cate) {
                                    $q->whereHas('item', function ($q2) use ($cate) {
                                        $q2->where(function ($q3) use ($cate) {
                                            $q3->whereHas('options', fn($q4) => $q4->where('cate', $cate))
                                               ->orWhereHas('option_pays', fn($q4) => $q4->where('cate', $cate));
                                        });
                                    });
                                }
                            })
                            ->when($request->filled('options'), function ($q) use ($request) {
                                foreach (array_map('intval', (array) $request->options) as $optionId) {
                                    $q->whereHas('item', fn($q2) => $q2->whereHas('options', fn($q3) => $q3->where('options.id', $optionId)));
                                }
                            })
                            ->when($request->filled('option_pays'), function ($q) use ($request) {
                                foreach (array_map('intval', (array) $request->option_pays) as $payId) {
                                    $q->whereHas('item', fn($q2) => $q2->whereHas('option_pays', fn($q3) => $q3->where('option_pays.id', $payId)));
                                }
                            })
                            ->orderBy('sort', 'asc') // ソートキーがあるなら
                            ->orderBy('time_start', 'asc')
                            ->get();

        // 予約データを取得（ローカルデータベース）
        try {
            // 各Scheduleに該当日付の予約をセット
            $schedules->each(function ($schedule) use ($adjustedDate) {
                try {
                    // 該当日の予約を取得
                    $reservations = Reservation::where('item_id', $schedule->item_id)
                        ->whereDate('date', $adjustedDate)
                        ->whereIn('flag', [1,2]) // 確認中または確定のみ表示
                        ->get();

                    $schedule->setRelation('reservations', $reservations);
                } catch (\Exception $e) {
                    \Log::warning('Failed to fetch reservations for schedule', [
                        'schedule_id' => $schedule->id,
                        'error' => $e->getMessage()
                    ]);
                    // エラー時は空のコレクションをセット
                    $schedule->setRelation('reservations', collect());
                }
            });

            // スタジオ（設備）のリスト

            // スタジオ（設備）の予約を取得
            $equipments = Equipment::all();

            // 各Equipmentに該当日付の予約をセット
            $equipments->each(function ($equipment) use ($adjustedDate) {
                try {
                    // equipment_idを使用して検索
                    $equipmentId = $equipment->id;

                    // 該当日のスタジオ予約を取得
                    $reservations = Reservation::where('equipment_id', $equipmentId)
                        ->whereDate('date', $adjustedDate)
                        ->where('flag', '!=', 1) // 削除済み以外
                        ->get();

                    $equipment->setRelation('reservations', $reservations);
                } catch (\Exception $e) {
                    \Log::warning('Failed to fetch equipment reservations', [
                        'equipment_id' => $equipment->id,
                        'error' => $e->getMessage()
                    ]);
                    // エラー時は空のコレクションをセット
                    $equipment->setRelation('reservations', collect());
                }
            });
        } catch (\Exception $e) {
            \Log::error('Failed to initialize reservation model', [
                'error' => $e->getMessage()
            ]);
            // エラー時は空のEquipmentコレクションを作成
            $equipments = collect();
        }

        $item_categories = ItemCategory::orderBy('sort')->get();
        $optionCates = OptionCate::orderBy('sort')->get();
        $options = Option::orderBy('sort')->get();
        $optionPays = OptionPay::orderBy('sort')->get();

        return view('schedule', compact('schedules', 'adjustedDate', 'equipments', 'item_categories', 'optionCates', 'options', 'optionPays')); //, 'dates'
    }


/**
 * Ajaxで日付遷移用
 */

    public function dayList($date)
    {
        $adjustedDate = Carbon::parse($date);

        $schedules = Schedule::whereDate('date', $adjustedDate)
            ->with(['item', 'itemImage']) // itemImageを追加
            ->orderBy('sort', 'asc')
            ->orderBy('time_start', 'asc')
            ->get()
            ->each(function ($schedule) use ($adjustedDate) {
                // 予約データを取得
                $reservations = Reservation::where('item_id', $schedule->item_id)
                    ->whereDate('date', $adjustedDate)
                    ->where('flag', '!=', 1)
                    ->get();

                $schedule->setRelation('reservations', $reservations);
            })
            ->groupBy(function ($schedule) {
                return Carbon::parse($schedule->date)->format('Y-m-d');
            });

        return view('partials.schedule_list', compact('schedules', 'adjustedDate'));
    }

}
