<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleMonthController extends Controller
{
    public function index($year = null, $month = null)
    {
        // 認証されたアイテムを取得
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        // 年と月が指定されていない場合、現在の年と月を使用
        $date = Carbon::now();
        if ($year && $month) {
            $date = Carbon::createFromDate($year, $month, 1);
        }

        // アイテム情報を取得（リレーション含む）
        $item = Item::with('thumbnail')->findOrFail($item->id);

        // スケジュールを取得し、日付をキーにして整理
        $schedulesCollection = Schedule::where('item_id', $item->id)
            ->whereYear('date', $date->year)
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

        return view('item.schedules_month.index', [
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
        // 認証されたアイテムを取得
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => '認証が必要です'], 401);
        }

        $date = $request->input('date');
        $itemId = $item->id;
        
        // セキュリティのため、送信されたitem_idと認証されたアイテムのIDが一致するか確認
        if ($request->has('item_id') && $request->input('item_id') != $itemId) {
            return response()->json(['error' => '権限がありません'], 403);
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

        // スケジュールが存在するかチェック
        $schedule = Schedule::where('item_id', $itemId)->where('date', $currentDate)->first();

        if ($schedule) {
            // 既存スケジュールがあれば削除
            $schedule->delete();
            return response()->json(['status' => 'deleted']);
        } else {
            // 新規スケジュールを作成
            // デフォルト出勤時間を取得（datetime castを回避してRAW文字列で取得）
            $itemRow = DB::table('items')->where('id', $itemId)->first();
            $defaultStartTime = $itemRow->default_sch_start ?? '06:00:00';
            $defaultEndTime   = $itemRow->default_sch_end   ?? '18:00:00';
            $tsStart = $this->timeToTimestamp($currentDate, $defaultStartTime);
            $tsEnd   = $this->timeToTimestamp($currentDate, $defaultEndTime);

            $schedules = Schedule::create([
                'item_id' => $itemId,
                'date' => $currentDate,
                'time_start' => $defaultStartTime,
                'time_end' => $defaultEndTime,
                'ts_start' => $tsStart,
                'ts_end' => $tsEnd,
                'status' => 0, // 0を固定で入力
                'sort' => 0,   // 0を固定で入力
                'flag' => 1,   // 出勤申請中
            ]);

            return response()->json(['status' => 'created', 'sch_id' => $schedules->id]);
        }
    }

    // 出勤時間やステータスの更新用のAJAXメソッド
    public function updateSchedule(Request $request)
    {
        // 認証されたアイテムを取得
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => '認証が必要です'], 401);
        }

        $scheduleId = $request->input('schedule_id');
        $field = $request->input('field');
        $value = $request->input('value');

        // スケジュールが認証されたアイテムのものか確認
        $schedule = Schedule::where('id', $scheduleId)
            ->where('item_id', $item->id)
            ->firstOrFail();

        // タイムスタンプ計算用に時間を調整（拡張形式はそのまま保存するが、Carbon用に変換）
        $tsValue = $value;
        $tsAddDay = false;
        if (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $value, $matches)) {
            $hours = (int)$matches[1];
            $minutes = (int)$matches[2];
            $seconds = (int)$matches[3];
            if ($hours >= 24) {
                $tsValue = sprintf('%02d:%02d:%02d', $hours - 24, $minutes, $seconds);
                $tsAddDay = true;
            }
        }

        // 時間の更新時は申請フラグを設定
        if (in_array($field, ['time_start', 'time_end'])) {
            // 申請用のフィールドに保存（拡張形式のまま）
            if ($field === 'time_start') {
                $schedule->request_time_start = $value;
                $ts = Carbon::parse($schedule->date . ' ' . $tsValue);
                if ($tsAddDay) $ts->addDay();
                $schedule->request_ts_start = $ts->timestamp;
            } else {
                $schedule->request_time_end = $value;
                $ts = Carbon::parse($schedule->date . ' ' . $tsValue);
                if ($tsAddDay) $ts->addDay();
                $schedule->request_ts_end = $ts->timestamp;
            }
            
            // 既に申請中でない場合は変更申請として設定
            if ($schedule->flag == 0) {
                $schedule->flag = 3; // 変更申請中
            } elseif ($schedule->flag == 1) {
                $schedule->flag = 4; // 出勤申請+変更申請
            }
        } else {
            // ステータスの更新
            $schedule->$field = $value;
        }

        $schedule->save();

        return response()->json(['status' => 'updated']);
    }

    // Ajaxでスケジュールを削除
    public function destroy($id)
    {
        // 認証されたアイテムを取得
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => '認証が必要です'], 401);
        }

        // スケジュールが認証されたアイテムのものか確認
        $schedule = Schedule::where('id', $id)
            ->where('item_id', $item->id)
            ->firstOrFail();

        // 申請中の場合は削除申請として設定
        if ($schedule->flag == 0) {
            $schedule->flag = 2; // 取消申請中
            $schedule->save();
            return response()->json(['message' => '取消申請を送信しました'], 200);
        } else {
            // 既に申請中の場合は削除
            $schedule->delete();
            return response()->json(['message' => 'スケジュールが削除されました'], 200);
        }
    }

    // 申請を取り消す
    public function cancelRequest(Request $request)
    {
        // 認証されたアイテムを取得
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => '認証が必要です'], 401);
        }

        $date = $request->input('date');

        // 指定された日付の申請中のスケジュールを取得
        $schedules = Schedule::where('item_id', $item->id)
            ->where('date', $date)
            ->whereIn('flag', [1, 2, 3, 4])
            ->get();

        foreach ($schedules as $schedule) {
            if ($schedule->flag == 1 || $schedule->flag == 4) {
                // 出勤申請中の場合は削除
                $schedule->delete();
            } else {
                // 変更申請または取消申請の場合は元に戻す
                $schedule->flag = 0;
                $schedule->request_time_start = null;
                $schedule->request_time_end = null;
                $schedule->request_ts_start = null;
                $schedule->request_ts_end = null;
                $schedule->save();
            }
        }

        return response()->json(['message' => '申請を取り消しました'], 200);
    }

    // デフォルト出勤時間を更新
    public function updateDefaultTimes(Request $request)
    {
        // 認証されたアイテムを取得
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $item = Item::findOrFail($item->id);

        $defaultStartTime = $request->input('default_sch_start');
        $defaultEndTime = $request->input('default_sch_end');

        // datetime castを回避してRAW文字列で直接保存（翌〇時など24時超も対応）
        DB::table('items')->where('id', $item->id)->update([
            'default_sch_start' => $defaultStartTime ?: null,
            'default_sch_end'   => $defaultEndTime   ?: null,
        ]);

        return redirect()->back()->with('success', 'デフォルト出勤時間を更新しました');
    }


    /**
     * 翌〇時（24時超）を含む時間文字列をUNIXタイムスタンプに変換
     */
    private function timeToTimestamp(string $date, string $time): int
    {
        $parts = explode(':', $time);
        $hours   = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);
        $seconds = (int)($parts[2] ?? 0);

        if ($hours >= 24) {
            $adjusted = sprintf('%02d:%02d:%02d', $hours - 24, $minutes, $seconds);
            return Carbon::parse($date . ' ' . $adjusted)->addDay()->timestamp;
        }

        return Carbon::parse($date . ' ' . $time)->timestamp;
    }
}

