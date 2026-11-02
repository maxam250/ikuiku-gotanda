<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Schedule;
// use App\Helpers\Helper;
use Carbon\Carbon;


class ScheduleMonthController extends Controller
{
    //
    public function index($itemId = null, $year = null, $month = null)
    {
        // 年と月が指定されていない場合、現在の年と月を使用
        $date = Carbon::now();
        if ($year && $month) {
            $date = Carbon::createFromDate($year, $month, 1);
        }


        $item_lists = Item::where('item_active', 1)->get(); // リスト用全キャストの取得

        //初期状態　itemId/year/monthが空の状態
        if(!isset($itemId)){
            return view('admin.schedules_month.default',compact('item_lists'));
        }

        $item = Item::with('itemImage')->findOrFail($itemId); // キャストの取得


        // $schedules = Schedule::where('item_id', $itemId)->whereMonth('date', $date->month)->get(); // キャストのスケジュール取得

        // スケジュールを取得し、日付をキーにして整理
        $schedulesCollection = Schedule::where('item_id', $itemId)
        ->whereMonth('date', $date->month)
        ->get();

        $schedules = [];
        foreach ($schedulesCollection as $schedule) {
            $formattedDate = Carbon::parse($schedule->date)->format('Y-m-d');
            if (!isset($schedules[$formattedDate])) {
                $schedules[$formattedDate] = [];
            }
            $schedules[$formattedDate][] = $schedule;
        }

        // カレンダーを生成
        $calendar = generateCalendar($date->year, $date->month);

        // 前月と翌月の計算
        $prevMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();



        return view('admin.schedules_month.index', [
            'item_lists' => $item_lists,
            'item' => $item,
            'schedules' => $schedules,
            'calendar' => $calendar,
            'currentYear' => $date->year,
            'currentMonth' => $date->month,
            'prevYear' => $prevMonth->year,
            'prevMonth' => $prevMonth->month,
            'nextYear' => $nextMonth->year,
            'nextMonth' => $nextMonth->month,
        ]);
    }



    // 出勤の登録・削除用のAJAXメソッド
    public function toggleSchedule(Request $request)
    {
        $date = $request->input('date');
        $itemId = $request->input('item_id');

        // スケジュールが存在するかチェック
        $schedule = Schedule::where('item_id', $itemId)->where('date', $date)->first();


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


        if ($schedule) {
            // 既存スケジュールがあれば削除
            $schedule->delete();
            $this->deleteOldSchedules();
            return response()->json(['status' => 'deleted']);
        } else {
            // 新規スケジュールを作成
            // 開始・終了時間のタイムスタンプを取得
            // $timeStart = Carbon::parse($currentDate . ' ' . $itemData['time_start']);
            // $timeEnd = Carbon::parse($currentDate . ' ' . $itemData['time_end'] );
            // $tsStart = $timeStart->timestamp;
            // $tsEnd = $timeEnd->timestamp;


            // デフォルト出勤時間を取得
            $item = Item::find($itemId);
            $defaultStartTime = $item->default_sch_start ? $item->default_sch_start->format('H:i:s') : '06:00:00';
            $defaultEndTime = $item->default_sch_end ? $item->default_sch_end->format('H:i:s') : '18:00:00';

            $timeStart = Carbon::parse($currentDate . ' ' . $defaultStartTime);
            $timeEnd = Carbon::parse($currentDate . ' ' . $defaultEndTime );
            $tsStart = $timeStart->timestamp;
            $tsEnd = $timeEnd->timestamp;


            $schedules = Schedule::create([
                'item_id' => $itemId,
                'date' => $date,
                'time_start' => $defaultStartTime,
                'time_end' => $defaultEndTime,
                'ts_start' => $tsStart,
                'ts_end' => $tsEnd,
                'status' => 0, // 0を固定で入力
                'sort' => 0   // 0を固定で入力
            ]);



            $this->deleteOldSchedules();
            return response()->json(['status' => 'created', 'sch_id' => $schedules->id]);
        }
    }



    // 出勤時間やステータスの更新用のAJAXメソッド
    public function updateSchedule(Request $request)
    {
        $scheduleId = $request->input('schedule_id');
        $field = $request->input('field');
        $value = $request->input('value');

        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->$field = $value;

        $schedule->save();

        $this->deleteOldSchedules();

        return response()->json(['status' => 'updated']);
    }


    // Ajaxでスケジュールを削除
    public function destroy($id)
    {
        // 指定されたIDのスケジュールを取得
        $schedule = Schedule::find($id);

        if ($schedule) {
            $schedule->delete();
            $this->deleteOldSchedules();

            return response()->json(['message' => 'スケジュールが削除されました!!!'], 200);
        } else {
            return response()->json(['message' => 'スケジュールが見つかりません'], 404);
        }
    }

    private function deleteOldSchedules(): void
    {
        Schedule::where('date', '<', Carbon::today()->subWeeks(2))->delete();
    }

}
