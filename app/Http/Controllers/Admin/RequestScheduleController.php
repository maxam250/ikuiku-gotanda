<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;

class RequestScheduleController extends Controller
{
    public function index()
    {
        // flag=1,2,3 の申請中データを取得
        $requests = Schedule::whereIn('flag', [1, 2, 3, 4])
            ->orderBy('date', 'asc')
            ->with('item') // キャスト情報が必要なら
            ->get();

        return view('admin.request_schedules.index', compact('requests'));
    }

    /**
     * 一括承認：一覧に表示されている申請をすべて承認する。
     */
    public function approveBulk(Request $request)
    {
        $request->validate([
            'schedule_ids'   => 'required|array',
            'schedule_ids.*' => 'integer|exists:schedules,id',
        ]);

        $ids = $request->input('schedule_ids', []);
        $schedules = Schedule::whereIn('id', $ids)->whereIn('flag', [1, 2, 3, 4])->get();

        $count = 0;
        $deleted = 0;

        foreach ($schedules as $schedule) {
            switch ($schedule->flag) {
                case 1: // 出勤申請
                    $schedule->flag = 0;
                    $schedule->save();
                    $count++;
                    break;
                case 2: // 削除申請
                    $schedule->delete();
                    $deleted++;
                    break;
                case 3: // 変更申請
                    if (!is_null($schedule->request_time_start)) {
                        $schedule->time_start = $schedule->request_time_start;
                        $schedule->ts_start = $schedule->request_ts_start;
                    }
                    if (!is_null($schedule->request_time_end)) {
                        $schedule->time_end = $schedule->request_time_end;
                        $schedule->ts_end = $schedule->request_ts_end;
                    }
                    $schedule->request_time_start = null;
                    $schedule->request_time_end = null;
                    $schedule->request_ts_start = null;
                    $schedule->request_ts_end = null;
                    $schedule->flag = 0;
                    $schedule->save();
                    $count++;
                    break;
                case 4: // 出勤申請+変更申請
                    if (!is_null($schedule->request_time_start)) {
                        $schedule->time_start = $schedule->request_time_start;
                        $schedule->ts_start = $schedule->request_ts_start;
                    }
                    if (!is_null($schedule->request_time_end)) {
                        $schedule->time_end = $schedule->request_time_end;
                        $schedule->ts_end = $schedule->request_ts_end;
                    }
                    $schedule->request_time_start = null;
                    $schedule->request_time_end = null;
                    $schedule->request_ts_start = null;
                    $schedule->request_ts_end = null;
                    $schedule->flag = 0;
                    $schedule->save();
                    $count++;
                    break;
            }
        }

        $total = $count + $deleted;
        $message = $total > 0
            ? "{$total} 件を一括承認しました。"
            : '承認対象がありませんでした。';

        return back()->with('success', $message);
    }

    public function approve(Request $request)
    {
        $schedule = Schedule::findOrFail($request->input('schedule_id'));
// dd($schedule);
        switch ($schedule->flag) {
            case 1: // 出勤申請
                $schedule->flag = 0;
                break;
            case 2: // 削除申請
                $schedule->delete();
                return back()->with('success', 'スケジュールを削除しました');
            case 3: // 変更申請
                // dd($schedule);
                // $schedule->time_start = $schedule->request_time_start;
                // $schedule->ts_start = $schedule->request_ts_start;
                // $schedule->time_end = $schedule->request_time_end;
                // $schedule->ts_end = $schedule->request_ts_end;
                if (!is_null($schedule->request_time_start)) {
                    $schedule->time_start = $schedule->request_time_start;
                    $schedule->ts_start = $schedule->request_ts_start;
                }
            
                if (!is_null($schedule->request_time_end)) {
                    $schedule->time_end = $schedule->request_time_end;
                    $schedule->ts_end = $schedule->request_ts_end;
                }
                $schedule->request_time_start = null;
                $schedule->request_time_end = null;
                $schedule->request_ts_start = null;
                $schedule->request_ts_end = null;
                $schedule->flag = 0;
                break;
            case 4: // 出勤申請+変更申請
                // $schedule->time_start = $schedule->request_time_start;
                // $schedule->ts_start = $schedule->request_ts_start;
                // $schedule->time_end = $schedule->request_time_end;
                // $schedule->ts_end = $schedule->request_ts_end;
                if (!is_null($schedule->request_time_start)) {
                    $schedule->time_start = $schedule->request_time_start;
                    $schedule->ts_start = $schedule->request_ts_start;
                }
            
                if (!is_null($schedule->request_time_end)) {
                    $schedule->time_end = $schedule->request_time_end;
                    $schedule->ts_end = $schedule->request_ts_end;
                }

                $schedule->request_time_start = null;
                $schedule->request_time_end = null;
                $schedule->request_ts_start = null;
                $schedule->request_ts_end = null;
                $schedule->flag = 0;
                break;
        }

        $schedule->save();
        return back()->with('success', '承認しました');
    }

    public function reject(Request $request)
    {
        $schedule = Schedule::findOrFail($request->input('schedule_id'));


        switch ($schedule->flag) {
            case 1: // 出勤申請
                $schedule->delete();
                return back()->with('success', 'スケジュールを削除しました');
                break;
            case 2: // 削除申請
                $schedule->flag = 0;
                break;
            case 3: // 変更申請
                // 申請内容を破棄し、元に戻す
                $schedule->request_time_start = null;
                $schedule->request_time_end = null;
                $schedule->request_ts_start = null;
                $schedule->request_ts_end = null;
                $schedule->flag = 0;
                break;
            case 4: // 出勤申請
                $schedule->delete();
                return back()->with('success', 'スケジュールを削除しました');
                break;
            }
        $schedule->save();
        return back()->with('success', '申請を却下しました');
    }
}
