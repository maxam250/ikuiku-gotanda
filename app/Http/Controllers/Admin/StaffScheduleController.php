<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffSchedule;
use App\Models\Staff;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffScheduleController extends Controller
{
    /**
     * スタッフスケジュール一覧（月別カレンダー）
     */
    public function index(Request $request)
    {
        $year  = (int) $request->get('year',  date('Y'));
        $month = (int) $request->get('month', date('m'));

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $prevMonth   = $currentDate->copy()->subMonth();
        $nextMonth   = $currentDate->copy()->addMonth();

        $calendar = $this->generateCalendar($year, $month);

        $startDate = $currentDate->copy()->startOfMonth()->format('Y-m-d');
        $endDate   = $currentDate->copy()->endOfMonth()->format('Y-m-d');

        $schedules = StaffSchedule::with(['staff', 'office'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('schedule_type', 'work')
            ->get()
            ->groupBy(fn($s) => $s->date->format('Y-m-d'));

        $offices = Office::where('is_active', 1)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        $staffs = Staff::where('is_active', 1)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        return view('admin.staff_schedule.index', compact(
            'year', 'month', 'prevMonth', 'nextMonth',
            'calendar', 'schedules', 'offices', 'staffs'
        ));
    }

    /**
     * スケジュール保存（追加・更新）/ 削除
     */
    public function store(Request $request)
    {
        try {
            // 削除
            if ($request->boolean('delete_schedule')) {
                $deleted = StaffSchedule::where('staff_id', $request->staff_id)
                    ->where('date', $request->date)
                    ->where('office_id', $request->office_id)
                    ->delete();

                return response()->json([
                    'success' => (bool) $deleted,
                    'message' => $deleted ? 'スケジュールを削除しました' : '対象が見つかりませんでした',
                ]);
            }

            $request->validate([
                'date'              => 'required|date',
                'staff_id'          => 'required|exists:staffs,id',
                'office_id' => 'nullable|exists:offices,id',
            ]);

            // 編集時：元レコードを削除
            if ($request->filled('original_staff_id') && $request->filled('original_date')) {
                StaffSchedule::where('staff_id', $request->original_staff_id)
                    ->where('date', $request->original_date)
                    ->where('office_id', $request->original_office_id)
                    ->delete();
            }

            StaffSchedule::updateOrCreate(
                [
                    'staff_id'          => $request->staff_id,
                    'office_id' => $request->office_id ?: null,
                    'date'              => $request->date,
                ],
                [
                    'schedule_type' => 'work',
                    'memo'          => $request->memo,
                ]
            );

            return response()->json(['success' => true, 'message' => '保存しました']);

        } catch (\Exception $e) {
            Log::error('StaffSchedule store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => '保存に失敗しました'], 500);
        }
    }

    /**
     * スケジュール1件取得（Ajax）
     */
    public function getSchedule(Request $request)
    {
        $schedule = StaffSchedule::where('staff_id', $request->staff_id)
            ->where('date', $request->date)
            ->where('office_id', $request->office_id ?: null)
            ->first();

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'スケジュールが見つかりません'], 404);
        }

        return response()->json([
            'success'            => true,
            'staff_id'           => $schedule->staff_id,
            'office_id'  => $schedule->office_id,
            'date'               => $schedule->date->format('Y-m-d'),
            'memo'               => $schedule->memo,
        ]);
    }

    /**
     * スケジュール削除（POST）
     */
    public function destroy(Request $request)
    {
        try {
            $deleted = StaffSchedule::where('staff_id', $request->staff_id)
                ->where('date', $request->date)
                ->where('office_id', $request->office_id ?: null)
                ->delete();

            return response()->json([
                'success' => (bool) $deleted,
                'message' => $deleted ? '削除しました' : '対象が見つかりません',
            ]);
        } catch (\Exception $e) {
            Log::error('StaffSchedule destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => '削除に失敗しました'], 500);
        }
    }

    /**
     * スタッフスケジュール一括設定（表示月の全日程）
     * weekly_schedule JSON の曜日・事務所設定に従って登録する
     */
    public function bulkStore(Request $request)
    {
        try {
            $request->validate([
                'bulk_year'  => 'required|integer|min:2000|max:2100',
                'bulk_month' => 'required|integer|min:1|max:12',
                'staffs'     => 'nullable|array',
            ]);

            $year  = (int) $request->bulk_year;
            $month = (int) $request->bulk_month;
            $items = $request->input('staffs', []);

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate   = $startDate->copy()->endOfMonth();

            $created = 0;
            $skipped = 0;

            foreach ($items as $staffId => $data) {
                if (empty($data['selected'])) {
                    continue;
                }

                $staff = Staff::find((int) $staffId);
                if (!$staff) {
                    continue;
                }

                $memo           = $data['memo'] ?? null;
                $weeklySchedule = $staff->weekly_schedule ?? [];

                $date = $startDate->copy();
                while ($date->lte($endDate)) {
                    // ISOウィークデー: 1=月, 2=火, ..., 7=日
                    $isoDay     = (string) $date->isoWeekday();
                    $dayConfig  = $weeklySchedule[$isoDay] ?? null;

                    if ($dayConfig && isset($dayConfig['is_work']) && $dayConfig['is_work'] == '1') {
                        $officeId = !empty($dayConfig['cate_id']) ? (int) $dayConfig['cate_id'] : null;

                        $schedule = StaffSchedule::firstOrCreate(
                            [
                                'staff_id'  => (int) $staffId,
                                'office_id' => $officeId,
                                'date'      => $date->format('Y-m-d'),
                            ],
                            [
                                'schedule_type' => 'work',
                                'memo'          => $memo,
                            ]
                        );

                        $schedule->wasRecentlyCreated ? $created++ : $skipped++;
                    }

                    $date->addDay();
                }
            }

            return redirect()
                ->route('admin.staff_schedule.index', ['year' => $year, 'month' => $month])
                ->with('success', "一括設定が完了しました（新規: {$created}件、スキップ: {$skipped}件）");

        } catch (\Exception $e) {
            Log::error('StaffSchedule bulkStore error: ' . $e->getMessage());
            return redirect()->back()->with('error', '一括設定に失敗しました');
        }
    }

    // -------------------------------------------------------------------------

    private function generateCalendar(int $year, int $month): array
    {
        $calendar = [];
        $date      = Carbon::createFromDate($year, $month, 1);
        $start     = $date->copy()->subDays($date->dayOfWeek);

        for ($week = 0; $week < 6; $week++) {
            $days = [];
            for ($day = 0; $day < 7; $day++) {
                $days[] = $start->copy()->addDays($week * 7 + $day);
            }
            $calendar[] = $days;
        }

        return $calendar;
    }
}
