<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Member;
use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\ItemImage;
use App\Models\OptionCate;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\CourseCate;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\shop;

use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmation;
use Carbon\Carbon;
use DateTime;
use DateInterval;

class HistoryController_old extends Controller
{

    public function index()
    {


        // 一年前とする場合はこちらを追加
        // where('date', '>=', Carbon::now()->subYear())

        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();

        $histories = Reservation::orderBy('id', 'desc')->get()->map(function($histories) {
            $histories->option = json_decode($histories->option, true);
            $histories->option_pay = json_decode($histories->option_pay, true);
            return $histories;
        });


        return view('item.histories.index', compact('histories', 'courses', 'course_fees', 'options', 'option_pays', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = Option::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        return view('item.histories.create', compact('items', 'courses', 'course_fees', 'options', 'option_pays', 'members'));
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


        // optionフィールドの処理 JSON文字列に変換
        $historyData['option'] = isset($historyData['option']) ? (is_array($historyData['option']) ? json_encode($historyData['option']) : $historyData['option']) : null;
        // telを結合
        $telParts = $historyData['tel'];
        $fullTel = implode('-', array_filter($telParts)); // 空の要素を除外
        $historyData['tel'] = $fullTel;

        //item_nameが空の時選択したitemの名前を挿入
        if ($historyData['item_name'] == null) {
            $get_item = Item::findOrFail($historyData['item_id']);
            $historyData['item_name'] = $get_item->item_name;
        }

        // セッションに保存する際は、結合した値を保存
        $request->session()->put('history_form_data', array_merge($request->all(), ['tel' => $fullTel]));



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
                    $historyData['time_end'] = $end_time->format('H:i:s');
                } else {
                    // course_timeが数値でない場合のエラーハンドリング
                    // ログに記録するか、デフォルト値を設定するなど
                    \Log::error("Invalid course_time value: " . $course_time);
                }
            }

            // 分を追加
            // $histories['time_end'] = $start_time->modify("+{$course_time} minutes")->format('H:i:s');
        }else{
            $historyData['time_end'] = null;
        }

        $historyData['fee_course'] = $historyData['fee_course'] ?? 0; // デフォルト値として0を設定
        $historyData['fee_option'] = $historyData['fee_option'] ?? 0; // デフォルト値として0を設定
        $historyData['fee_adjust'] = $historyData['fee_adjust'] ?? 0; // デフォルト値として0を設定

        $historyData['fee_total'] = ($historyData['fee_course'] ?? 0)
          + ($historyData['fee_option'] ?? 0)
          + ($historyData['fee_adjust'] ?? 0);



        $histories = Reservation::create($historyData);


        return redirect()->route('item.histories.index')->with('予約を登録しました');
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
        //
        $histories = Reservation::findOrFail($history);
        // JSONデータをPHP配列に変換
        $histories->option = json_decode($histories->option, true);
        $histories->option_pay = json_decode($histories->option_pay, true);

        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = Option::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();


        return view('item.histories.edit', compact('histories', 'items', 'courses', 'course_fees', 'options', 'option_pays', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $history)
{
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
    $history->update([
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

    return redirect()->route('item.histories.index')->with('success', '予約が更新されました');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $history)
    {
        //
        $histories = Reservation::findOrFail($history);


        // データの削除
        $histories->delete();

        return redirect()->route('item.histories.index')->with('success', '予約が削除されました');
    }



    public function updateFlag(Request $request)
    {

        $history = Reservation::findOrFail($request->history_id);
        $history->flag = $request->flag;
        $success = $history->save();

        return response()->json(['success' => $success]);
    }

}
