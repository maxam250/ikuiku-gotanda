<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\Equipment;
use App\Models\Costume;
use App\Models\Member;
use App\Services\ExternalDatabaseService;
use Carbon\Carbon;
use DateTime;
use DateInterval;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        // 現在時点の調整日付を取得
        $adjustedDate = adjustTimeWithShift(); //custom helper関数

        $items = Item::selectable($request->input('item_id'))->orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::where('active' , 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active',1)->orderBy('sort', 'asc')->get();
        $members = Member::select('id', 'member_no', 'name', 'tel')
            ->orderBy('id', 'asc')
            ->get();

        // 検索条件の取得
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $flag = $request->input('flag');
        $itemId = $request->input('item_id');
        $memberId = $request->input('member_id');

        // クエリビルダの開始
        $model = ExternalDatabaseService::getReservationModel();
        $query = $model::query();

        // 検索パラメータの設定
        $searchParams = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'flag' => $flag,
            'item_id' => $itemId,
            'member_id' => $memberId
        ];

        // 初期状態では何も表示しない（検索条件がある場合のみ表示）
        if ($dateFrom || $dateTo || $flag !== null || $itemId || $memberId) {
            $histories = $query->where(function($query) {
                $query->where('flag', 4)
                      ->orWhere('date', '<', Carbon::now()->startOfDay());
            })
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->where('date', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->where('date', '<=', $dateTo);
            })
            ->when($flag !== null, function($query) use ($flag) {
                return $query->where('flag', $flag);
            })
            ->when($itemId, function($query) use ($itemId) {
                return $query->where('item_id', $itemId);
            })
            ->when($memberId, function($query) use ($memberId) {
                return $query->where('member_id', $memberId);
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->through(function($reservation) {
                $reservation->option = json_decode($reservation->option, true);
                $reservation->option_pay = json_decode($reservation->option_pay, true);
                return $reservation;
            });
        } else {
            // 空のページネーションオブジェクトを作成
            $histories = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1,
                ['path' => request()->url()]
            );
        }

        return view('admin.histories.index', compact(
            'histories', 
            'courses', 
            'course_fees', 
            'options', 
            'option_pays', 
            'items', 
            'equipments', 
            'costumes',
            'members',
            'searchParams'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $items = Item::selectable()->orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        return view('admin.histories.create', compact('items', 'courses', 'course_fees', 'options', 'option_pays', 'members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'member_id' => 'nullable|integer',
            'tel.*' => 'nullable|numeric|digits_between:3,4',
            'tel' => 'nullable|array|size:3',
            'date' => 'required|date',
            'course_id' => 'required|integer',
            'course_time' => 'required|integer',
            'fee_course' => 'nullable|integer',
            'fee_option' => 'nullable|integer',
            'fee_adjust' => 'nullable|integer',
            'fee_total' => 'nullable|integer',
            // その他の必要なバリデーションルール
        ]);

        $historyData = $request->all();

        // optionフィールドの処理
        $option = isset($historyData['option']) ? (is_array($historyData['option']) ? json_encode($historyData['option']) : $historyData['option']) : null;

        // telを結合
        $fullTel = implode('-', array_filter($request->tel));

        // item_nameの処理
        $item_name = $historyData['item_name'] ?? Item::findOrFail($historyData['item_id'])->item_name;

        // time_endの計算
        $time_end = null;
        if (isset($historyData['time_start']) && isset($historyData['course_time'])) {
            $course_time = CourseFee::findOrFail($historyData['course_time'])->course_time;
            $time_start = DateTime::createFromFormat('H:i:s', $historyData['time_start']);
            if ($time_start !== false) {
                // course_timeが数値であることを確認し、DateInterval形式に変換
                if (is_numeric($course_time)) {
                    $interval = new DateInterval("PT" . $course_time . "M");
                    $end_time = (clone $time_start)->add($interval);
                    $time_end = $end_time->format('H:i:s');
                } else {
                    // course_timeが数値でない場合のエラーハンドリング
                    // ログに記録するか、デフォルト値を設定するなど
                    \Log::error("Invalid course_time value: " . $course_time);
                }
            }
        }

        //料金　
        $fee_course = $historyData['fee_course'] ?? 0; // デフォルト値として0を設定
        $fee_option = $historyData['fee_option'] ?? 0; // デフォルト値として0を設定
        $fee_adjust = $historyData['fee_adjust'] ?? 0; // デフォルト値として0を設定

        $fee_total = $fee_course
          + $fee_option
          + $fee_adjust;

        // 外部DBを使用して予約を作成
        $model = ExternalDatabaseService::getReservationModel();
        $reservations = $model::create($historyData);

        return redirect()->route('admin.histories.index')->with('success', '予約を登録しました');
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
    public function edit(string $history)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $reservations = $model::findOrFail($history);
        
        // JSONデータをPHP配列に変換
        $reservations->option = json_decode($reservations->option, true);
        $reservations->option_pay = json_decode($reservations->option_pay, true);

        $items = Item::selectable($reservations->item_id)->orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        return view('admin.histories.edit', compact('reservations', 'items', 'courses', 'course_fees', 'options', 'option_pays', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $history)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $historyModel = $model::findOrFail($history);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'member_id' => 'nullable|integer',
            'tel.*' => 'nullable|numeric|digits_between:3,4',
            'tel' => 'nullable|array|size:3',
            'date' => 'required|date',
            'course_id' => 'required|integer',
            'course_time' => 'required|integer',
            'fee_course' => 'nullable|integer',
            'fee_option' => 'nullable|integer',
            'fee_adjust' => 'nullable|integer',
            'fee_total' => 'nullable|integer',
            // その他の必要なバリデーションルール
        ]);

        $historyData = $request->all();

        // optionフィールドの処理
        $option = isset($historyData['option']) ? (is_array($historyData['option']) ? json_encode($historyData['option']) : $historyData['option']) : null;

        // telを結合
        $fullTel = implode('-', array_filter($request->tel));

        // item_nameの処理
        $item_name = $historyData['item_name'] ?? Item::findOrFail($historyData['item_id'])->item_name;

        // time_endの計算
        $time_end = null;
        if (isset($historyData['time_start']) && isset($historyData['course_time'])) {
            $course_time = CourseFee::findOrFail($historyData['course_time'])->course_time;
            $time_start = DateTime::createFromFormat('H:i:s', $historyData['time_start']);
            if ($time_start !== false) {
                // course_timeが数値であることを確認し、DateInterval形式に変換
                if (is_numeric($course_time)) {
                    $interval = new DateInterval("PT" . $course_time . "M");
                    $end_time = (clone $time_start)->add($interval);
                    $time_end = $end_time->format('H:i:s');
                } else {
                    // course_timeが数値でない場合のエラーハンドリング
                    // ログに記録するか、デフォルト値を設定するなど
                    \Log::error("Invalid course_time value: " . $course_time);
                }
            }
        }

        //料金　
        $fee_course = $historyData['fee_course'] ?? 0; // デフォルト値として0を設定
        $fee_option = $historyData['fee_option'] ?? 0; // デフォルト値として0を設定
        $fee_adjust = $historyData['fee_adjust'] ?? 0; // デフォルト値として0を設定

        $fee_total = $fee_course
          + $fee_option
          + $fee_adjust;

        // 更新
        $historyModel->update([
            'flag' => $request->flag ?? 0,
            'date' => $request->date,
            'kind' => $request->kind,
            'course_id' => $request->course_id,
            'course_time' => $request->course_time,
            'time_start' => $request->time_start,
            'time_end' => $time_end,
            'item_id' => $request->item_id,
            'item_name' => $item_name,
            'option' => $option,
            'name' => $request->name,
            'member_id' => $request->member_id,
            'email' => $request->email,
            'tel' => $fullTel,
            'area' => $request->area,
            'place' => $request->place,
            'address' => $request->address,
            'meet' => $request->meet,
            'comment' => $request->comment,
            'memo' => $request->memo,
            'memo_share' => $request->memo_share,
            'memo_result' => $request->memo_result,
            'fee_course' => $fee_course,
            'fee_option' => $fee_option,
            'fee_adjust' => $fee_adjust,
            'fee_adjust_memo' => $request->fee_adjust_memo,
            'fee_total' => $fee_total,
        ]);

        return redirect()->route('admin.histories.index')->with('success', '予約が更新されました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $history)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $reservations = $model::findOrFail($history);

        // データの削除
        $reservations->delete();

        return redirect()->route('admin.histories.index')->with('success', '予約が削除されました');
    }

    public function updateFlag(Request $request)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $history = $model::findOrFail($request->history_id);
        $history->flag = $request->flag;
        $success = $history->save();

        return response()->json(['success' => $success]);
    }
}
