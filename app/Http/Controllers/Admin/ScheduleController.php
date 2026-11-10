<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */


     public function index (Request $request, $date = null){

        // 現在時点の調整日付を取得
        $adjustedDate = adjustTimeWithShift(); //custom helper関数

        // URLに日付が指定されていれば、その日付を使用（有効な日付フォーマットのみ）
        if ($date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $adjustedDate = $date;
        }

        //調整日付をstringからdateとして扱えるようにする
        $adjustedDate = Carbon::parse($adjustedDate);

        $dates = getWeekDates(0, 2);

        //出勤者
        $schedules_pre = Schedule::with(['item', 'itemImage'])
        ->where('date', $adjustedDate)
        ->orderBy('sort', 'asc')
        ->orderBy('time_start', 'asc')
        ->get();

        // まず、出勤しているキャストのIDリストを取得
        $workingItemIds = Schedule::where('date', $adjustedDate)
        ->pluck('item_id')
        ->toArray();

        // 名前検索
        $search = trim($request->input('search', ''));

        // 出勤していないキャストを取得
        $non_schedules = Item::with('thumbnail')->selectable()
        ->whereNotIn('id', $workingItemIds)
        ->when($search !== '', fn($q) => $q->where('item_name', 'like', '%' . $search . '%'))
        ->paginate(100)
        ->appends($request->only('search'));


        // dd($schedules_pre);

        // グループ化を再適用
        $schedules = $schedules_pre->groupBy('date');

        // デフォルト出勤時間情報を各スケジュールに追加
        foreach ($schedules as $date => $scheduleGroup) {
            foreach ($scheduleGroup as $schedule) {
                $schedule->default_start_time = $schedule->item?->default_sch_start ? substr($schedule->item->default_sch_start, 0, 5) : null;
                $schedule->default_end_time   = $schedule->item?->default_sch_end   ? substr($schedule->item->default_sch_end, 0, 5)   : null;
            }
        }

        return view('admin.schedules.index', compact('schedules', 'non_schedules', 'adjustedDate', 'dates', 'search'));
    }



    // Ajax　出勤ステータスを更新する処理
    public function updateStatus(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        // ステータスを更新
        $schedule->status = $request->status;
        $schedule->save();

        $this->deleteOldSchedules();

        return response()->json(['success' => true, 'message' => 'ステータスが更新されました']);
    }



    // Ajax　並び順を更新する処理
    public function sort(Request $request)
    {
        $scheduleOrder = $request->input('scheduleOrder');

        foreach ($scheduleOrder as $order) {
            // 各スケジュールの並び順を更新
            Schedule::where('id', $order['id'])->update(['sort' => $order['sort']]);
        }

        $this->deleteOldSchedules();

        return response()->json(['success' => true]);
    }


    // Ajax　出勤時間を更新する処理
    // public function updateTime(Request $request, $id)
    // {
    //     $schedule = Schedule::findOrFail($id);


    //     // フィールド名と値を更新W
    //     if (in_array($request->field, ['time_start', 'time_end'])) {
    //         $schedule->{$request->field} = $request->value;
    //         $schedule->save();
    //     }

    //     return response()->json(['success' => true]);
    // }

    public function updateTime(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        if (in_array($request->field, ['time_start', 'time_end'])) {
            $schedule->{$request->field} = $request->value;
            $schedule->save();
        }

        $this->deleteOldSchedules();

        return response()->json(['success' => true]);
    }




    //Ajax　出勤から削除する処理
    public function destroy($id)
    {
        try {
            $schedule = Schedule::find($id);

            if ($schedule) {
                $schedule->delete();
                $this->deleteOldSchedules();
                return response()->json(['success' => true], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json(['success' => false, 'message' => 'スケジュールが見つかりません'], 404, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => '削除中にエラーが発生しました: ' . $e->getMessage()], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }



    // Ajax　出勤時間を更新する処理
    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        // time_start と time_end の処理
        $timeStart = $this->convertToDateTime($request->input('time_start'), $request->input('date'));
        $timeEnd = $this->convertToDateTime($request->input('time_end'), $request->input('date'));

        $schedule->time_start = $timeStart;
        $schedule->time_end = $timeEnd;
        $schedule->save();

        $this->deleteOldSchedules();

        return response()->json(['success' => true, 'message' => 'スケジュールが更新されました']);
    }


    // 24時を超える時間の処理
    private function convertToDateTime($time, $date)
    {
        // 時刻が24時間を超える場合の処理
        list($hour, $minute, $second) = explode(':', $time);
        $hour = (int)$hour;

        if ($hour >= 24) {
            // 24時間を超えた場合、翌日に変換
            $hour -= 24;
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        }

        return date('Y-m-d H:i:s', strtotime("$date $hour:$minute:$second"));
    }



    // 非出勤キャストを一括出勤
    public function bulkUpdate(Request $request)
    {
        $items = $request->input('items');
        $date = $request->input('schedule_date');

        // $itemsがnullまたは空の場合の処理
        if (empty($items) || !is_array($items)) {
            return redirect()->route('admin.schedules.index')->with('error', '出勤に設定するキャストが選択されていません。');
        }

        // パラメータの日付が指定されていない場合、$adjustedDateを使用
        if ($date === null) {
            $currentDate = adjustTimeWithShift();
        } else {
            // 指定された日付が正しいフォーマットかチェックして取得
            try {
                $currentDate = Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                // 日付のパースに失敗した場合は404ページにリダイレクト
                abort(404);
            }
        }
// dd($items);
        // スケジュールを更新または作成
        foreach ($items as $itemId => $itemData) {

            // チェックが入っているキャストのみ処理
            if (isset($itemData['selected']) && $itemData['selected'] == 1) {


                $time_start = $itemData['time_start'];
                $time_end   = $itemData['time_end'];

                // タイムスタンプ計算用に24時超を翌日実時刻に変換（time_start/time_endには使わない）
                $tsStart = Carbon::parse($this->convertToDateTime($time_start, $currentDate))->timestamp;
                $tsEnd   = Carbon::parse($this->convertToDateTime($time_end,   $currentDate))->timestamp;

                // 出勤スケジュールを作成または更新
                // time_start/time_endは25:00:00形式のまま保存する（翌表示・比較のため）
                Schedule::updateOrCreate(
                    ['item_id' => $itemId, 'date' => $currentDate],
                    [
                        'time_start' => $time_start,
                        'time_end'   => $time_end,
                        'ts_start'   => $tsStart,
                        'ts_end'     => $tsEnd,
                        'status'     => 0,
                        'sort'       => 0,
                    ]
                );
            }
        }

        $this->deleteOldSchedules();

        return redirect()->route('admin.schedules.index')->with('success', '出勤情報を一斉登録しました');
    }



    private function deleteOldSchedules(): void
    {
        Schedule::where('date', '<', Carbon::today()->subWeeks(2))->delete();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        //

        return view('admin.schedules.edit', compact('item'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

}
