<?php

namespace App\Http\Controllers\Item;

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


     public function index ($date = null){

        // 現在時点の調整日付を取得
        $adjustedDate = adjustTimeWithShift(); //custom helper関数

        // URLに日付が指定されていれば、その日付を使用
        if ($date) {
            $adjustedDate = $date;
        }

        //調整日付をstringからdateとして扱えるようにする
        $adjustedDate = Carbon::parse($adjustedDate);


        // 1週間分の日付リストを生成
        // $startOfWeek = Carbon::parse($date)->startOfWeek();
        // $endOfWeek = Carbon::parse($date)->endOfWeek();


        $dates = getWeekDates();

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

        // 出勤していないキャストを取得
        $non_schedules = Item::with('thumbnail')
        ->whereNotIn('id', $workingItemIds)  // 出勤キャストIDに含まれないキャストを取得
        ->get();


        // 開始時間、終了時間をCarbonフォーマット
        foreach ($schedules_pre as $schedule) {
            $schedules_pre->time_start_carbon = Carbon::parse($schedule->time_start)->format('H:i');
            $schedules_pre->time_end_carbon = Carbon::parse($schedule->time_end)->format('H:i');
        }

        // dd($schedules_pre);

        // グループ化を再適用
        $schedules = $schedules_pre->groupBy('date');


        return view('item.schedules.index', compact('schedules', 'non_schedules','adjustedDate', 'dates'));
    }



    // Ajax　出勤ステータスを更新する処理
    public function updateStatus(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        // ステータスを更新
        $schedule->status = $request->status;
        $schedule->save();

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
        $timeValue = $request->value;

        if (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $timeValue, $matches)) {
            $hours = (int)$matches[1];

            // ✅ stringをCarbonに変換
            $scheduleDate = Carbon::parse($schedule->date);

            if ($hours >= 24) {
                $adjustedHours = $hours - 24;
                $date = $scheduleDate->copy()->addDay();
                $timeValue = $date->format('Y-m-d') . ' ' . str_pad($adjustedHours, 2, '0', STR_PAD_LEFT) . ':' . $matches[2] . ':' . $matches[3];
            } else {
                $timeValue = $scheduleDate->format('Y-m-d') . ' ' . $timeValue;
            }
        }

        $schedule->{$request->field} = $timeValue;
        $schedule->save();
    }

    return response()->json(['success' => true]);
}




    //Ajax　出勤から削除する処理
    public function destroy($id)
    {
        $schedule = Schedule::find($id);

        if ($schedule) {
            $schedule->delete(); // スケジュールを削除
            return response()->json(['success' => true]);

        } else {
            return response()->json(['success' => false, 'message' => 'スケジュールが見つかりません']);
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


        // パラメータの日付が指定されていない場合、$adjustedDateを使用
        if ($date === null) {
            // 現在時刻を取得し、設定に従って日付を調整
            $timeShift = config('custom.time_shift');
            $now = Carbon::now();

            // 設定された時間だけ遅らせる
            $adjustedDateTime = $now->subHours($timeShift);

            // 現在の時間が5時以前の場合、前日を扱う
            if ($adjustedDateTime->hour < 5) {
                $currentDate = $adjustedDateTime->subDay()->format('Y-m-d');
            } else {
                $currentDate = $adjustedDateTime->format('Y-m-d');
            }
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


                $time_start =  $itemData['time_start'];
                $time_end =  $itemData['time_end'];
                // 開始・終了時間のタイムスタンプを取得


                // 00:00を超える時間は翌日のタイムスタンプに変換
                $adjustTimeStart = $this->convertToDateTime($itemData['time_start'], $currentDate);
                $adjustTimeEnd = $this->convertToDateTime($itemData['time_end'], $currentDate);

// dd($adjustTimeStart);
                $timeStart = Carbon::parse($adjustTimeStart);
                $timeEnd = Carbon::parse($adjustTimeEnd );
                $tsStart = $timeStart->timestamp;
                $tsEnd = $timeEnd->timestamp;

                // 出勤スケジュールを作成または更新
                Schedule::updateOrCreate(
                    ['item_id' => $itemId, 'date' => $currentDate], // キャストIDと日付で一意にする
                    [
                        'time_start' => $timeStart->format('H:i:s'),
                        'time_end' => $timeEnd->format('H:i:s'),
                        'ts_start' => $tsStart,
                        'ts_end' => $tsEnd,
                        'status' => 0, // 0を固定で入力
                        'sort' => 0   // 0を固定で入力
                    ]
                );
            }
        }

        return redirect()->back()->with('success', '出勤情報を一斉登録しました');
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

        return view('item.schedules.edit', compact('item'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

}
