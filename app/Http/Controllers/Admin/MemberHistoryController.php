<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\Item;
use App\Models\Course;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\CourseFee;
use App\Models\Equipment;
use App\Models\Costume;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MemberHistoryController extends Controller
{
    /**
     * 会員の利用履歴一覧を表示
     */
    public function index(Member $member, Request $request)
    {

        // 現在時点の調整日付を取得
        $adjustedDate = adjustTimeWithShift(); //custom helper関数

        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::where('active' , 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active',1)->orderBy('sort', 'asc')->get();
        $members = Member::select('id', 'member_no', 'name', 'tel')
            ->orderBy('id', 'asc')
            ->get();



        $query = Reservation::where('member_id', $member->id)
            ->with(['item', 'course']);

        // 日付範囲でのフィルタリング
        if ($request->filled('date_from')) {
            $query->where('reserved_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }
        if ($request->filled('date_to')) {
            $query->where('reserved_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // ステータスでのフィルタリング
        if ($request->filled('status')) {
            $query->where('flag', $request->status);
        }

        // 並び順
        $sort = $request->get('sort', 'reserved_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $histories = $query->paginate(20)->withQueryString();

        // 集計データの取得
        $stats = [
            'total_visits' => $query->where('flag', 2)->count(), // flag=2は確定を表す
            'total_amount' => $query->where('flag', 2)->sum('fee_total'),
            'avg_amount' => $query->where('flag', 2)->avg('fee_total'),
        ];

        return view('admin.member_histories.index', [
            'member' => $member,
            'histories' => $histories,
            'stats' => $stats,
            'filters' => $request->all(),
            'items' => $items,
            'options' => $options,
            'option_pays' => $option_pays,
            'courses' => $courses,
            'course_fees' => $course_fees,
            'equipments' => $equipments,
            'costumes' => $costumes,
            'members' => $members,
        ]);
    }

    /**
     * 新規予約作成フォームを表示
     */
    public function create(Member $member)
    {
        $members = Member::select('id', 'member_no', 'name', 'tel')
            ->orderBy('id', 'asc')
            ->get();
        $items = Item::where('item_active', 1)->get();
        $courses = Course::where('active', 1)->get();
        $items = Item::where('item_active', 1)->get();
        $courses = Course::where('active', 1)->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::where('active' , 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active',1)->orderBy('sort', 'asc')->get();

        return view('admin.member_histories.create', [
            'member' => $member,
            'members' => $members,
            'items' => $items,
            'courses' => $courses,
            'options' => $options,
            'option_pays' => $option_pays,
            'course_fees' => $course_fees,
            'equipments' => $equipments,
            'costumes' => $costumes,
        ]);
    }

    /**
     * 新規予約を保存
     */
    public function store(Request $request, Member $member)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'time_start' => 'nullable|date_format:H:i:s',
            'item_id' => 'required|exists:items,id',
            'course_id' => 'required|exists:courses,id',
            'flag' => 'required|in:0,1,2,3', // 0:未 1:確認 2:確定 3:保留
            'memo' => 'nullable|string|max:1000',
            'memo_share' => 'nullable|string|max:1000',
            'fee_total' => 'required_if:flag,2|nullable|integer|min:0',
        ]);

        // 選択されたitemの名前を取得
        $item = Item::find($validated['item_id']);
        $validated['item_name'] = $item->item_name;

        $reservation = new Reservation($validated);
        $reservation->member_id = $member->id;

        // reserved_atから日付と時間を分離して設定
        $reservedAt = null;
        if ($request->filled('date') && $request->filled('time_start')) {
            $reservation->reserved_at = $request->date . ' ' . $request->time_start;
        }

        // reserved_atから日付と時間を分離して設定
        // $reservedAt = Carbon::parse($validated['reserved_at']);
        // $reservation->date = $reservedAt->toDateString();
        // $reservation->time_start = $reservedAt->format('H:i:00');

        $reservation->save();

        return redirect()
            ->route('admin.member_histories.index', $member)
            ->with('success', '予約を作成しました。');
    }

    /**
     * 予約編集フォームを表示
     */
    public function edit(Member $member, Reservation $history)
    {
        $members = Member::select('id', 'member_no', 'name', 'tel')
            ->orderBy('id', 'asc')
            ->get();
        $items = Item::where('item_active', 1)->get();
        $courses = Course::where('active', 1)->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::where('active' , 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active',1)->orderBy('sort', 'asc')->get();

        return view('admin.member_histories.edit', [
            'member' => $member,
            'members' => $members,
            'history' => $history,
            'items' => $items,
            'courses' => $courses,
            'options' => $options,
            'option_pays' => $option_pays,
            'course_fees' => $course_fees,
            'equipments' => $equipments,
            'costumes' => $costumes,
        ]);
    }

    /**
     * 予約を更新
     */
    public function update(Request $request, Member $member, Reservation $history)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'time_start' => 'nullable|date_format:H:i:s',
            'item_id' => 'required|exists:items,id',
            'course_id' => 'required|exists:courses,id',
            'flag' => 'required|in:0,1,2,3', // 0:未 1:確認 2:確定 3:保留
            'memo' => 'nullable|string|max:1000',
            'memo_share' => 'nullable|string|max:1000',
            'fee_total' => 'required_if:flag,2|nullable|integer|min:0',
        ]);

        // 選択されたitemの名前を取得
        $item = Item::find($validated['item_id']);
        $validated['item_name'] = $item->item_name;

        // reserved_atから日付と時間を分離して設定
        $reservedAt = null;
        if ($request->filled('date') && $request->filled('time_start')) {
            $reservedAt = $request->date . ' ' . $request->time_start;
        }

        // $reservedAt = Carbon::parse($validated['reserved_at']);
        // $validated['date'] = $reservedAt->toDateString();
        // $validated['time_start'] = $reservedAt->format('H:i:00');

        $history->update($validated);

        return redirect()
            ->route('admin.member_histories.index', $member)
            ->with('success', '予約を更新しました。');
    }

    /**
     * CSVエクスポート
     */
    public function export(Member $member)
    {
        $histories = Reservation::where('member_id', $member->id)
            ->with(['item', 'course'])
            ->orderBy('reserved_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $member->name . '_利用履歴.csv"',
            'Pragma' => 'no-cache',
        ];

        $callback = function() use ($histories) {
            $file = fopen('php://output', 'w');

            // ヘッダー行
            fputcsv($file, [
                '予約日時',
                '担当キャスト',
                'コース',
                'ステータス',
                '金額',
                'メモ',
                'キャスト共有メモ',
            ]);

            // データ行
            foreach ($histories as $history) {
                $status = match($history->flag) {
                    0 => '未',
                    1 => '確認',
                    2 => '確定',
                    3 => '保留',
                    default => '不明'
                };

                fputcsv($file, [
                    $history->reserved_at->format('Y-m-d H:i'),
                    optional($history->item)->item_name ?? '未定',
                    optional($history->course)->title ?? '-',
                    $status,
                    $history->fee_total ?? 0,
                    $history->memo ?? '',
                    $history->memo_share ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
