<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Item;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\Equipment;
use App\Models\Costume;
use App\Models\Member;
use App\Services\ExternalDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        // 外部DB対応
        $reservationModel = ExternalDatabaseService::getReservationModel();
        
        // 検索条件
        $query = $reservationModel::where('item_id', $item->id);

        // 日付範囲検索
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        } else {
            // デフォルトは本日以降
            $query->where('date', '>=', Carbon::today());
        }

        // ステータス検索
        if ($request->filled('flag')) {
            $query->where('flag', $request->flag);
        } else {
            // デフォルトはキャンセル以外
            $query->where('flag', '!=', 4);
        }

        // 予約一覧を取得
        $reservations = $query->orderBy('date', 'asc')
            ->orderBy('time_start', 'asc')
            ->paginate(20);

        return view('item.reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $equipments = Equipment::where('active', 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        return view('item.reservations.create', compact(
            'item',
            'courses',
            'course_fees',
            'options',
            'option_pays',
            'equipments',
            'costumes',
            'members'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'member_id' => 'nullable|integer',
            'tel.*' => 'nullable|numeric|digits_between:3,4',
            'tel' => 'nullable|array|size:3',
            'date' => 'required|date',
            'course_id' => 'required|integer',
            'course_time' => 'required|integer',
            'time_start' => 'required|date_format:H:i',
            'fee_course' => 'nullable|integer',
            'fee_option' => 'nullable|integer',
            'fee_adjust' => 'nullable|integer',
            'fee_total' => 'nullable|integer',
        ]);

        $reservationData = $request->all();
        $reservationData['item_id'] = $item->id;
        $reservationData['item_name'] = $item->item_name;

        // optionフィールドが配列の場合、JSON文字列に変換
        if (isset($reservationData['option']) && is_array($reservationData['option'])) {
            $reservationData['option'] = json_encode($reservationData['option']);
        } else {
            $reservationData['option'] = null;
        }

        // option_payフィールドが配列の場合、JSON文字列に変換
        if (isset($reservationData['option_pay']) && is_array($reservationData['option_pay'])) {
            $reservationData['option_pay'] = json_encode($reservationData['option_pay']);
        } else {
            $reservationData['option_pay'] = null;
        }

        // myoptionフィールドが配列の場合、JSON文字列に変換
        if (isset($reservationData['myoption']) && is_array($reservationData['myoption'])) {
            $reservationData['myoption'] = json_encode($reservationData['myoption']);
        } else {
            $reservationData['myoption'] = null;
        }

        // mycostumeフィールドが配列の場合、JSON文字列に変換
        if (isset($reservationData['mycostume']) && is_array($reservationData['mycostume'])) {
            $reservationData['mycostume'] = json_encode($reservationData['mycostume']);
        } else {
            $reservationData['mycostume'] = null;
        }

        // コスチューム関連の配列をJSON形式で保存
        $costumes = [];
        if (!empty($reservationData['costume1'])) $costumes[] = $reservationData['costume1'];
        if (!empty($reservationData['costume2'])) $costumes[] = $reservationData['costume2'];
        if (!empty($reservationData['costume3'])) $costumes[] = $reservationData['costume3'];
        $reservationData['costume'] = !empty($costumes) ? json_encode($costumes) : null;

        // telを結合
        $telParts = $reservationData['tel'] ?? [];
        $fullTel = implode('-', array_filter($telParts));
        $reservationData['tel'] = $fullTel;

        // time_endの計算
        if (isset($reservationData['time_start']) && isset($reservationData['course_time'])) {
            $time_start = Carbon::createFromFormat('H:i', $reservationData['time_start']);
            $time_end = $time_start->copy()->addMinutes($reservationData['course_time']);
            $reservationData['time_end'] = $time_end->format('H:i:s');
        }

        // 料金の計算
        $reservationData['fee_course'] = $reservationData['fee_course'] ?? 0;
        $reservationData['fee_option'] = $reservationData['fee_option'] ?? 0;
        $reservationData['fee_adjust'] = $reservationData['fee_adjust'] ?? 0;
        $reservationData['fee_myoption'] = $reservationData['fee_myoption'] ?? 0;
        $reservationData['fee_mycostume'] = $reservationData['fee_mycostume'] ?? 0;
        $reservationData['fee_total'] = 
            ($reservationData['fee_course'] ?? 0) +
            ($reservationData['fee_option'] ?? 0) +
            ($reservationData['fee_adjust'] ?? 0) +
            ($reservationData['fee_myoption'] ?? 0) +
            ($reservationData['fee_mycostume'] ?? 0);

        // shop_idを設定
        $shop = \App\Models\Shop::first();
        $reservationData['shop_id'] = $shop->id ?? 1;

        // デフォルト値
        $reservationData['flag'] = $reservationData['flag'] ?? 0;
        $reservationData['kind'] = $reservationData['kind'] ?? 1;

        // 予約を作成
        $reservationModel = ExternalDatabaseService::getReservationModel();
        $reservation = $reservationModel::create($reservationData);

        return redirect()->route('item.reservations.index')
            ->with('success', '予約を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $reservationModel = ExternalDatabaseService::getReservationModel();
        $reservation = $reservationModel::where('item_id', $item->id)->findOrFail($id);

        return view('item.reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $reservationModel = ExternalDatabaseService::getReservationModel();
        $reservation = $reservationModel::where('item_id', $item->id)->findOrFail($id);

        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $equipments = Equipment::where('active', 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        return view('item.reservations.edit', compact(
            'reservation',
            'item',
            'courses',
            'course_fees',
            'options',
            'option_pays',
            'equipments',
            'costumes',
            'members'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $reservationModel = ExternalDatabaseService::getReservationModel();
        $reservation = $reservationModel::where('item_id', $item->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'member_id' => 'nullable|integer',
            'tel.*' => 'nullable|numeric|digits_between:3,4',
            'tel' => 'nullable|array|size:3',
            'date' => 'required|date',
            'course_id' => 'required|integer',
            'course_time' => 'required|integer',
            'time_start' => 'required|date_format:H:i',
            'fee_course' => 'nullable|integer',
            'fee_option' => 'nullable|integer',
            'fee_adjust' => 'nullable|integer',
            'fee_total' => 'nullable|integer',
        ]);

        $reservationData = $request->all();

        // optionフィールドが配列の場合、JSON文字列に変換
        if (isset($reservationData['option']) && is_array($reservationData['option'])) {
            $reservationData['option'] = json_encode($reservationData['option']);
        } else {
            $reservationData['option'] = null;
        }

        // option_payフィールドが配列の場合、JSON文字列に変換
        if (isset($reservationData['option_pay']) && is_array($reservationData['option_pay'])) {
            $reservationData['option_pay'] = json_encode($reservationData['option_pay']);
        } else {
            $reservationData['option_pay'] = null;
        }

        // myoptionフィールドが配列の場合、JSON文字列に変換
        if (isset($reservationData['myoption']) && is_array($reservationData['myoption'])) {
            $reservationData['myoption'] = json_encode($reservationData['myoption']);
        } else {
            $reservationData['myoption'] = null;
        }

        // mycostumeフィールドが配列の場合、JSON文字列に変換
        if (isset($reservationData['mycostume']) && is_array($reservationData['mycostume'])) {
            $reservationData['mycostume'] = json_encode($reservationData['mycostume']);
        } else {
            $reservationData['mycostume'] = null;
        }

        // コスチューム関連の配列をJSON形式で保存
        $costumes = [];
        if (!empty($reservationData['costume1'])) $costumes[] = $reservationData['costume1'];
        if (!empty($reservationData['costume2'])) $costumes[] = $reservationData['costume2'];
        if (!empty($reservationData['costume3'])) $costumes[] = $reservationData['costume3'];
        $reservationData['costume'] = !empty($costumes) ? json_encode($costumes) : null;

        // telを結合
        $telParts = $reservationData['tel'] ?? [];
        $fullTel = implode('-', array_filter($telParts));
        $reservationData['tel'] = $fullTel;

        // time_endの計算
        if (isset($reservationData['time_start']) && isset($reservationData['course_time'])) {
            $time_start = Carbon::createFromFormat('H:i', $reservationData['time_start']);
            $time_end = $time_start->copy()->addMinutes($reservationData['course_time']);
            $reservationData['time_end'] = $time_end->format('H:i:s');
        }

        // 料金の計算
        $reservationData['fee_course'] = $reservationData['fee_course'] ?? 0;
        $reservationData['fee_option'] = $reservationData['fee_option'] ?? 0;
        $reservationData['fee_adjust'] = $reservationData['fee_adjust'] ?? 0;
        $reservationData['fee_myoption'] = $reservationData['fee_myoption'] ?? 0;
        $reservationData['fee_mycostume'] = $reservationData['fee_mycostume'] ?? 0;
        $reservationData['fee_total'] = 
            ($reservationData['fee_course'] ?? 0) +
            ($reservationData['fee_option'] ?? 0) +
            ($reservationData['fee_adjust'] ?? 0) +
            ($reservationData['fee_myoption'] ?? 0) +
            ($reservationData['fee_mycostume'] ?? 0);

        $reservation->update($reservationData);

        return redirect()->route('item.reservations.index')
            ->with('success', '予約を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $reservationModel = ExternalDatabaseService::getReservationModel();
        $reservation = $reservationModel::where('item_id', $item->id)->findOrFail($id);
        $reservation->delete();

        return redirect()->route('item.reservations.index')
            ->with('success', '予約を削除しました。');
    }

    /**
     * Update reservation flag
     */
    public function updateFlag(Request $request, $id)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $reservationModel = ExternalDatabaseService::getReservationModel();
        $reservation = $reservationModel::where('item_id', $item->id)->findOrFail($id);

        $validated = $request->validate([
            'flag' => 'required|integer|in:0,1,2,3,4',
        ]);

        $reservation->update(['flag' => $validated['flag']]);

        return response()->json([
            'success' => true,
            'message' => 'ステータスを更新しました。',
            'flag' => $reservation->flag
        ]);
    }
}

