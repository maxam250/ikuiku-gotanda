<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation; // reservationを追加
use App\Models\Shop;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\OptionCate;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\CourseCate;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\Member;
use App\Models\Equipment;
use App\Models\Costume;
use App\Models\Link;
use App\Models\Staff;
use App\Services\ExternalDatabaseService;



// use Illuminate\Support\Facades\Mail;
// use App\Mail\ReservationConfirmation;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;


class ReservationController_old extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
        $links = Link::where('active', 1)
            ->orderBy('position', 'asc')
            ->orderBy('sort', 'asc')
            ->get();

        // 検索条件の取得
        $date = $request->input('date');
        $flag = $request->input('flag');
        $itemId = $request->input('item_id');
        $memberId = $request->input('member_id');

// dd($items);
        // if ($reservations->email) {
        //     try {
        //         $reservations->email = \Illuminate\Support\Facades\Crypt::decryptString($reservations->email);
        //     } catch (\Exception $e) {
        //         // 復号化に失敗した場合は元の値を使用
        //     }
        // }


        // ローカルDBを使用
        \Log::info('Using Local Database for Reservations');

        // クエリビルダの開始
        $model = ExternalDatabaseService::getReservationModel();
        $query = $model::query();
        $shop = Shop::first();

        // shop_idのデバッグ情報
        \Log::info('Shop Info:', [
            'shop_id' => $shop->id,
            'shop_idname' => $shop->shop_idname
        ]);

        // shop_idの条件を追加（整数型のIDを使用）
        $query->where('shop_id', $shop->id ?? 1);

        // 日付での検索
        if ($date) {
            $query->whereDate('date', $date);
        } else {
            // 日付指定がない場合は本日以降を表示
            $query->where('date', '>=', \Carbon\Carbon::parse($adjustedDate));
        }

        // ステータスでの検索
        if ($flag !== null) {
            $query->where('flag', $flag);
        } else {
            // ステータス指定がない場合はキャンセル以外を表示
            $query->where(function ($q) {
                $q->where('flag', '!=', 4)
                ->orWhereNull('flag');
            });
        }
// $reservations = $query->orderBy('id', 'desc')->limit(10)->get();
// dd($reservations);
        // キャストでの検索
        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        // 会員での検索
        if ($memberId) {
            $query->where('member_id', $memberId);
        }

        // 予約データの取得
        try {
            $reservations = $query->orderBy('id', 'desc')
                ->get()
                ->map(function($reservation) {
                    // optionフィールドの処理
                    if (is_string($reservation->option)) {
                        $reservation->option = json_decode($reservation->option, true);
                    }
                    // option_payフィールドの処理
                    if (is_string($reservation->option_pay)) {
                        $reservation->option_pay = json_decode($reservation->option_pay, true);
                    }
                    return $reservation;
                });
        } catch (\Illuminate\Database\QueryException $e) {
            // データベース接続エラーの場合
            \Log::error('Failed to fetch reservations in index', [
                'error' => $e->getMessage(),
                'shop_id' => $shop->id ?? null,
                'is_external' => ExternalDatabaseService::isExternalEnabled()
            ]);

            // エラー時は空のコレクションを返す
            $reservations = collect();

            // ユーザーにエラーメッセージを表示
            $errorMessage = ExternalDatabaseService::isExternalEnabled() 
                ? '外部データベースへの接続に失敗しました。しばらく待ってから再度お試しください。'
                : 'データベースへの接続に失敗しました。';
            return back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in reservations index', [
                'error' => $e->getMessage(),
                'shop_id' => $shop->id ?? null,
                'is_external' => ExternalDatabaseService::isExternalEnabled()
            ]);

            $reservations = collect();
            return back()->with('error', 'データの取得中にエラーが発生しました。');
        }

        // デバッグ情報を追加
        \Log::info('Reservations Query Result:', [
            'count' => $reservations->count(),
            'shop_id' => $shop->id,
            'shop_idname' => $shop->shop_idname,
            'query_sql' => $query->toSql(),
            'query_bindings' => $query->getBindings()
        ]);

        // ローカルDBのデータ確認
        try {
            $totalReservations = \DB::table('reservations')
                ->count();
            \Log::info('Total reservations in local DB:', ['count' => $totalReservations]);

            $shopReservations = \DB::table('reservations')
                ->where('shop_id', $shop->id ?? 1)
                ->count();
            \Log::info('Reservations for shop_id in local DB:', [
                'shop_id' => $shop->id,
                'shop_idname' => $shop->shop_idname,
                'count' => $shopReservations
            ]);
        } catch (\Exception $e) {
            \Log::error('Local DB Data Check Error:', [
                'message' => $e->getMessage()
            ]);
        }

        //staff
        try {
            $model2 = ExternalDatabaseService::getStaffModel();
            $query2 = $model2::query();
            $staffs = $query2->orderBy('id', 'asc')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            // テーブルが存在しない場合やその他のDBエラーの場合
            if (str_contains($e->getMessage(), "doesn't exist") || str_contains($e->getMessage(), 'Base table or view not found')) {
                \Log::info('staffs table does not exist, using empty collection', [
                    'error' => $e->getMessage()
                ]);
                $staffs = collect();
            } else {
                \Log::warning('Failed to get staffs', [
                    'error' => $e->getMessage()
                ]);
                $staffs = collect();
            }
        } catch (\Exception $e) {
            // その他のエラーの場合
            \Log::warning('Failed to get staffs', [
                'error' => $e->getMessage()
            ]);
            $staffs = collect();
        }


        // 検索条件をビューに渡す
        $searchParams = [
            'date' => $date,
            'flag' => $flag,
            'item_id' => $itemId,
            'member_id' => $memberId
        ];

        return view('admin.reservations.index', compact(
            'reservations',
            'courses',
            'course_fees',
            'options',
            'option_pays',
            'items',
            'equipments',
            'costumes',
            'members',
            'staffs',
            'links',
            'searchParams'
        ));
    }

    /**
     * 印刷用ビュー
     */
    public function printView(Request $request)
    {
        $adjustedDate = adjustTimeWithShift();

        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::where('active', 1)->orderBy('sort', 'asc')->get();

        $singleId = $request->input('single_id');
        $date     = $request->input('date');
        $flag     = $request->input('flag');
        $itemId   = $request->input('item_id');
        $memberId = $request->input('member_id');

        $model = ExternalDatabaseService::getReservationModel();
        $shop  = Shop::first();

        // 単票モード（1件詳細印刷）
        if ($singleId) {
            $reservation = $model::where('shop_id', $shop->id ?? 1)->findOrFail($singleId);
            if (is_string($reservation->option))     { $reservation->option     = json_decode($reservation->option, true); }
            if (is_string($reservation->option_pay)) { $reservation->option_pay = json_decode($reservation->option_pay, true); }

            return view('admin.reservations.print_single', compact(
                'reservation', 'courses', 'course_fees', 'options', 'option_pays',
                'items', 'equipments', 'shop'
            ));
        }

        // 一覧モード
        $query = $model::query()->where('shop_id', $shop->id ?? 1);

        if ($date) {
            $query->whereDate('date', $date);
        } else {
            $query->where('date', '>=', \Carbon\Carbon::parse($adjustedDate));
        }

        if ($flag !== null && $flag !== '') {
            $query->where('flag', $flag);
        } else {
            $query->where(function ($q) {
                $q->where('flag', '!=', 4)->orWhereNull('flag');
            });
        }

        if ($itemId)   { $query->where('item_id', $itemId); }
        if ($memberId) { $query->where('member_id', $memberId); }

        $reservations = $query->orderBy('date', 'asc')->orderBy('time_start', 'asc')->get()
            ->map(function ($reservation) {
                if (is_string($reservation->option))     { $reservation->option     = json_decode($reservation->option, true); }
                if (is_string($reservation->option_pay)) { $reservation->option_pay = json_decode($reservation->option_pay, true); }
                return $reservation;
            });

        $searchParams = compact('date', 'flag', 'itemId', 'memberId');

        return view('admin.reservations.print', compact(
            'reservations', 'courses', 'course_fees', 'options', 'option_pays',
            'items', 'equipments', 'searchParams'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();
        $equipments = Equipment::where('active' , 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::orderBy('sort', 'asc')->get();
        $costumeCates = \App\Models\CostumeCate::where('active', 1)
            ->with(['costumes' => function($query) {
                $query->where('active', 1)->orderBy('name', 'asc');
            }])
            ->orderBy('sort', 'asc')
            ->get();
        $staffs = Staff::where('is_active', 1)->orderBy('name', 'asc')->get();

        return view('admin.reservations.create', compact('items', 'courses', 'course_fees', 'options', 'option_pays', 'members', 'equipments', 'costumes', 'costumeCates', 'staffs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'item_name' => 'nullable|string|max:255',
                'item_name_other' => 'nullable|string|max:255',
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }


        $reservationData = $request->all();


        // optionフィールドが配列の場合、JSON文字列に変換
        if(isset($reservationData['option'])){
            if (is_array($reservationData['option'])) {
                $reservationData['option'] = json_encode($reservationData['option']);
            }
        }else{
            $reservationData['option'] = null;
        }

        // option_payフィールドが配列の場合、JSON文字列に変換
        if(isset($reservationData['option_pay'])){
            if (is_array($reservationData['option_pay'])) {
                $reservationData['option_pay'] = json_encode($reservationData['option_pay']);
            }
        }else{
            $reservationData['option_pay'] = null;
        }

        // myoptionフィールドが配列の場合、JSON文字列に変換
        if(isset($reservationData['myoption'])){
            if (is_array($reservationData['myoption'])) {
                $reservationData['myoption'] = json_encode($reservationData['myoption']);
            }
        }else{
            $reservationData['myoption'] = null;
        }

        // mycostumeフィールドが配列の場合、JSON文字列に変換
        if(isset($reservationData['mycostume'])){
            if (is_array($reservationData['mycostume'])) {
                $reservationData['mycostume'] = json_encode($reservationData['mycostume']);
            }
        }else{
            $reservationData['mycostume'] = null;
        }

        // コスチューム関連の配列をJSON形式で保存
        $costumes = [];
        if (!empty($reservationData['costume1'])) $costumes[] = $reservationData['costume1'];
        if (!empty($reservationData['costume2'])) $costumes[] = $reservationData['costume2'];
        if (!empty($reservationData['costume3'])) $costumes[] = $reservationData['costume3'];
        $reservationData['costume'] = !empty($costumes) ? json_encode($costumes) : null;

        // 個別のcostume1～3は削除（JSON形式の配列に統合したため）
        unset($reservationData['costume1']);
        unset($reservationData['costume2']);
        unset($reservationData['costume3']);

        // telを結合
        $telParts = $reservationData['tel'];
        $fullTel = implode('-', array_filter($telParts)); // 空の要素を除外
        $reservationData['tel'] = $fullTel;

        //item_nameが空の時選択したitemの名前を挿入
        if ($reservationData['item_name'] == null) {
            $get_item = Item::findOrFail($reservationData['item_id']);
            $reservationData['item_name'] = $get_item->item_name;
        }

        // セッションに保存する際は、結合した値を保存
        $request->session()->put('reservation_form_data', array_merge($request->all(), ['tel' => $fullTel]));



        // time_endの計算
        $time_end = null;
        if (isset($reservationData['time_start']) && isset($reservationData['course_time'])) {
            // course_timeは直接分単位の数値なので、そのまま使用
            $course_time = $reservationData['course_time'];
            $time_start = DateTime::createFromFormat('H:i:s', $reservationData['time_start']);
            if ($time_start !== false) {
                // course_timeが数値であることを確認し、DateInterval形式に変換
                if (is_numeric($course_time)) {
                    $interval = new DateInterval("PT" . $course_time . "M");
                    $end_time = (clone $time_start)->add($interval);
                    $reservationData['time_end'] = $end_time->format('H:i:s');
                } else {
                    // course_timeが数値でない場合のエラーハンドリング
                    \Log::error("Invalid course_time value: " . $course_time);
                    $reservationData['time_end'] = null;
                }
            } else {
                \Log::error("Invalid time_start format: " . $reservationData['time_start']);
                $reservationData['time_end'] = null;
            }
        } else {
            $reservationData['time_end'] = null;
        }

        $reservationData['fee_course'] = $reservationData['fee_course'] ?? 0; // デフォルト値として0を設定
        $reservationData['fee_option'] = $reservationData['fee_option'] ?? 0; // デフォルト値として0を設定
        $reservationData['fee_adjust'] = $reservationData['fee_adjust'] ?? 0; // デフォルト値として0を設定
        $reservationData['fee_myoption'] = $reservationData['fee_myoption'] ?? 0; // デフォルト値として0を設定
        $reservationData['fee_mycostume'] = $reservationData['fee_mycostume'] ?? 0; // デフォルト値として0を設定

        $reservationData['fee_total'] = ($reservationData['fee_course'] ?? 0)
          + ($reservationData['fee_option'] ?? 0)
          + ($reservationData['fee_adjust'] ?? 0)
          + ($reservationData['fee_myoption'] ?? 0)
          + ($reservationData['fee_mycostume'] ?? 0);



        // shop_idを設定（整数型のIDを使用）
        $shops = Shop::first();
        $reservationData['shop_id'] = $shops->id ?? 1; // shop_idnameではなくidを使用

        try {
            $reservations = ExternalDatabaseService::createReservation($reservationData);
        } catch (\Exception $e) {
            \Log::error('Failed to create reservation:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => '予約の作成に失敗しました: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.reservations.index')->with('success', '予約を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $shop = Shop::first();
        $reservation = $model::where('shop_id', $shop->id ?? 1)->findOrFail($id);

        // ローカルサーバーからitemとcourseの情報を取得
        if ($reservation->item_id) {
            $item = \App\Models\Item::find($reservation->item_id);
            $reservation->item = $item;
        }

        if ($reservation->course_id) {
            $course = \App\Models\Course::find($reservation->course_id);
            $reservation->course = $course;
        }

        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $reservation)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $shop = Shop::first();
        $reservations = $model::where('shop_id', $shop->id ?? 1)->findOrFail($reservation);

        // ローカルサーバーからitemとcourseの情報を取得
        if ($reservations->item_id) {
            $item = \App\Models\Item::find($reservations->item_id);
            $reservations->item = $item;
        }

        if ($reservations->course_id) {
            $course = \App\Models\Course::find($reservations->course_id);
            $reservations->course = $course;
        }

        // メールアドレスの復号化
        if ($reservations->email) {
            try {
                $reservations->email = \Illuminate\Support\Facades\Crypt::decryptString($reservations->email);
            } catch (\Exception $e) {
                // 復号化に失敗した場合は元の値を使用
            }
        }

        // JSONデータをPHP配列に変換（nullの場合は空配列を設定）
        $reservations->option = is_string($reservations->option) ? json_decode($reservations->option, true) : ($reservations->option ?? []);
        $reservations->option_pay = is_string($reservations->option_pay) ? json_decode($reservations->option_pay, true) : ($reservations->option_pay ?? []);
        $reservations->costume = is_string($reservations->costume) ? json_decode($reservations->costume, true) : ($reservations->costume ?? []);
        $reservations->myoption = is_string($reservations->myoption) ? json_decode($reservations->myoption, true) : ($reservations->myoption ?? []);
        $reservations->mycostume = is_string($reservations->mycostume) ? json_decode($reservations->mycostume, true) : ($reservations->mycostume ?? []);

        // equipment_idがexternal_idの値になっている場合、ローカルのidに変換
        if ($reservations->equipment_id) {
            $equipment = Equipment::where('external_id', $reservations->equipment_id)->first();
            if ($equipment) {
                $reservations->equipment_id = $equipment->id;
            }
        }

        // 新しい料金計算システム
        $courseFee = 0; // Initialize to 0
        if (!empty($reservations->item_id)) { // Only calculate if item_id exists
            $courseFee = $this->calculateCourseFee($reservations->item_id, $reservations->course_id, $reservations->course_time);
        }

        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();
        $equipments = Equipment::where('active' , 1)->orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active',1)->orderBy('sort', 'asc')->get();
        $costumeCates = \App\Models\CostumeCate::where('active', 1)
            ->with(['costumes' => function($query) {
                $query->where('active', 1)->orderBy('name', 'asc');
            }])
            ->orderBy('sort', 'asc')
            ->get();

        $staffs = \App\Models\Staff::where('is_active', 1)
            ->orderBy('sort')->orderBy('name')->get();

        return view('admin.reservations.edit', compact('reservations', 'items', 'courses', 'course_fees', 'options', 'option_pays', 'members', 'equipments', 'costumes', 'costumeCates', 'staffs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $shop = Shop::first();
        $reservation = $model::where('shop_id', $shop->id ?? 1)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_name' => 'nullable|string|max:255',
            'item_name_other' => 'nullable|string|max:255',
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

        $reservationData = $request->all();


        // メールアドレスの暗号化 →　モデルで暗号化しているためコメントアウト
        // if ($request->email) {
        //     try {
        //         $reservationData['email'] = \Illuminate\Support\Facades\Crypt::encryptString($request->email);
        //     } catch (\Exception $e) {
        //         // 暗号化に失敗した場合はそのまま使用
        //     }
        // }

        // JSONデータの処理
        $reservationData['option'] = !empty($reservationData['option']) ? json_encode($reservationData['option']) : null;
        $reservationData['option_pay'] = !empty($reservationData['option_pay']) ? json_encode($reservationData['option_pay']) : null;
        $reservationData['myoption'] = !empty($reservationData['myoption']) ? json_encode($reservationData['myoption']) : null;
        $reservationData['mycostume'] = !empty($reservationData['mycostume']) ? json_encode($reservationData['mycostume']) : null;

        // コスチューム関連の配列をJSON形式で保存
        $costumes = [];
        if (!empty($reservationData['costume1'])) $costumes[] = $reservationData['costume1'];
        if (!empty($reservationData['costume2'])) $costumes[] = $reservationData['costume2'];
        if (!empty($reservationData['costume3'])) $costumes[] = $reservationData['costume3'];
        $reservationData['costume'] = !empty($costumes) ? json_encode($costumes) : null;

        // 個別のcostume1～3は削除（JSON形式の配列に統合したため）
        unset($reservationData['costume1']);
        unset($reservationData['costume2']);
        unset($reservationData['costume3']);

        // telを結合
        $fullTel = implode('-', array_filter($request->tel));

        // item_nameの処理
        $item_name = $reservationData['item_name'] ?? Item::findOrFail($reservationData['item_id'])->item_name;

        // time_endの計算
        $time_end = null;
        if (isset($reservationData['time_start']) && isset($reservationData['course_time'])) {
            // course_timeは直接分単位の数値なので、そのまま使用
            $course_time = $reservationData['course_time'];
            $time_start = DateTime::createFromFormat('H:i:s', $reservationData['time_start']);
            if ($time_start !== false) {
                if (is_numeric($course_time)) {
                    $interval = new DateInterval("PT" . $course_time . "M");
                    $end_time = (clone $time_start)->add($interval);
                    $time_end = $end_time->format('H:i:s');
                } else {
                    \Log::error("Invalid course_time value: " . $course_time);
                    $time_end = null;
                }
            } else {
                \Log::error("Invalid time_start format: " . $reservationData['time_start']);
                $time_end = null;
            }
        }

        //料金
        $fee_course = $reservationData['fee_course'] ?? 0;
        $fee_option = $reservationData['fee_option'] ?? 0;
        $fee_adjust = $reservationData['fee_adjust'] ?? 0;
        $fee_myoption = $reservationData['fee_myoption'] ?? 0;
        $fee_mycostume = $reservationData['fee_mycostume'] ?? 0;

        $fee_total = $fee_course
          + $fee_option
          + $fee_adjust
          + $fee_myoption
          + $fee_mycostume;

        // equipment_idのexternal_id変換
        $equipmentId = $request->equipment_id;
        if ($equipmentId) {
            $equipment = Equipment::find($equipmentId);
            if ($equipment && $equipment->external_id) {
                $equipmentId = $equipment->external_id;
            }
        }

        // 更新
        $reservation->update([
            'flag' => $request->flag ?? 0,
            'date' => $request->date,
            'kind' => $request->kind,
            'course_id' => $request->course_id,
            'course_time' => $request->course_time,
            'time_start' => $request->time_start,
            'time_end' => $time_end,
            'item_id' => $request->item_id,
            'item_name' => $item_name,
            'item_name_other' => $request->item_name_other,
            'option' => $reservationData['option'],
            'option_pay' => $reservationData['option_pay'],
            'name' => $request->name,
            'member_id' => $request->member_id,
            'email' => $reservationData['email'],
            'tel' => $fullTel,
            'costume' => $reservationData['costume'],
            'myoption' => $reservationData['myoption'],
            'mycostume' => $reservationData['mycostume'],
            'equipment_id' => $equipmentId,
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
            'fee_myoption' => $fee_myoption,
            'fee_mycostume' => $fee_mycostume,
            'fee_total' => $fee_total,
            'fee_adjust_memo' => $request->fee_adjust_memo,
            'staff_id'        => $request->staff_id ?: null,
        ]);

        // 担当スタッフをセッションに保存
        if ($request->staff_id) {
            $staff = \App\Models\Staff::find($request->staff_id);
            if ($staff) {
                session(['selected_staff' => ['id' => $staff->id, 'name' => $staff->name, 'role' => $staff->role]]);
            }
        }

        return redirect()->route('admin.reservations.edit', $id)->with('success', '予約を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $reservation)
    {
        //
        $model = ExternalDatabaseService::getReservationModel();
        $shop = Shop::first();
        $reservations = $model::where('shop_id', $shop->id ?? 1)->findOrFail($reservation);


        // データの削除
        $reservations->delete();

        return redirect()->route('admin.reservations.index')->with('success', '予約が削除されました');
    }



    public function updateFlag(Request $request)
    {
        $shop = Shop::first();
        $model = ExternalDatabaseService::getReservationModel();
        $reservation = $model::where('shop_id', $shop->id ?? 1)->findOrFail($request->reservation_id);
        $reservation->flag = $request->flag;
        $success = $reservation->save();

        return response()->json(['success' => $success]);
    }

    /**
     * 未対応予約の件数を取得
     */
    public function getPendingCount()
    {
        try {
            // キャッシュキー
            $cacheKey = 'pending_reservations_count';

            // キャッシュから取得（1分間キャッシュ）
            $cachedData = \Cache::get($cacheKey);
            if ($cachedData !== null) {
                return response()->json($cachedData, 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $shop = Shop::first();
            if (!$shop) {
                $response = [
                    'count' => 0,
                    'display' => '0'
                ];
                \Cache::put($cacheKey, $response, 60); // 1分間キャッシュ
                return response()->json($response, 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            // ローカルDBから取得
            $model = ExternalDatabaseService::getReservationModel();
            $count = $model::where('shop_id', $shop->id ?? 1)
                ->where('flag', 0)
                ->where('created_at', '>=', \Carbon\Carbon::today())
                ->count();

            $response = [
                'count' => $count,
                'display' => $count > 5 ? '5+' : (string)$count
            ];

            // 正常に取得できた場合は1分間キャッシュ
            \Cache::put($cacheKey, $response, 60);

            return response()->json($response, 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Error in getPendingCount: ' . $e->getMessage());

            $response = [
                'count' => 0,
                'display' => '-'
            ];
            // エラー時は短時間キャッシュ
            \Cache::put('pending_reservations_count', $response, 30);

            return response()->json($response, 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get cast items (myoptions and mycostumes)
     */
    public function getCastItems($itemId)
    {
        $item = Item::with(['myOptions', 'myCostumes'])->findOrFail($itemId);

        // コレクションを配列に変換し、必要なデータのみを抽出
        $myOptions = $item->myOptions->map(function ($option) {
            return [
                'id' => $option->id,
                'name' => $option->name,
                'filename' => $option->filename,
                'price' => $option->price,
                'image_url' => $option->filename
                    ? Storage::url('myoption/'.$option->filename)
                    : '',
            ];
        })->values()->all();

        $myCostumes = $item->myCostumes->map(function ($costume) {
            return [
                'id' => $costume->id,
                'name' => $costume->name,
                'filename' => $costume->filename,
                'price' => $costume->price,
                'image_url' => $costume->filename
                    ? Storage::url('mycostume/'.$costume->filename)
                    : '',
            ];
        })->values()->all();

        \Log::info('Cast items data:', [
            'myOptions' => $myOptions,
            'myCostumes' => $myCostumes
        ]);

        return response()->json([
            'myOptions' => $myOptions,
            'myCostumes' => $myCostumes
        ]);
    }

    /**
     * 料金計算API
     */
    public function calculateFees(Request $request)
    {
        $courseId = $request->input('course_id');
        $courseTime = $request->input('course_time');
        $itemId = $request->input('item_id');

        try {
            // 新しい料金計算システムを使用
            $courseFee = $this->calculateCourseFee($itemId, $courseId, $courseTime);

            // オプション料金は0（管理画面では手動入力）
            $optionFee = 0;
            $myoptionFee = 0;
            $mycostumeFee = 0;

            return response()->json([
                'success' => true,
                'fee_course' => $courseFee,
                'fee_option' => $optionFee,
                'fee_myoption' => $myoptionFee,
                'fee_mycostume' => $mycostumeFee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 新しい料金計算システム
     * item_course_feesテーブルから料金を取得
     */
    private function calculateCourseFee($itemId, $courseId, $courseTime)
    {
        try {
            // item_course_feesテーブルから料金設定を取得
            $feeSetting = \DB::table('item_course_fees')
                ->where('item_id', $itemId)
                ->where('course_id', $courseId)
                ->first();

            if (!$feeSetting) {
                return 0;
            }

            // 一律料金を使用する場合
            if ($feeSetting->use_uniform_rate && $feeSetting->uniform_rate) {
                $fee = $feeSetting->uniform_rate * ($courseTime / 30);
                return $fee;
            }

            // 180分までは個別料金を使用
            if ($courseTime <= 180) {
                $feeColumn = 'fee_' . $courseTime;
                if (property_exists($feeSetting, $feeColumn) && $feeSetting->$feeColumn !== null) {
                    return $feeSetting->$feeColumn;
                }
            }

            // 210分以上は延長料金を上乗せ
            if ($courseTime >= 210) {
                $baseFee = 0;
                if (property_exists($feeSetting, 'fee_180') && $feeSetting->fee_180 !== null) {
                    $baseFee = $feeSetting->fee_180;
                }
                $extensionMinutes = $courseTime - 180;
                $extensionUnits = ceil($extensionMinutes / 30);
                $extensionFee = 0;
                if ($feeSetting->fee_extension !== null) {
                    $extensionFee = $feeSetting->fee_extension * $extensionUnits;
                }
                $totalFee = $baseFee + $extensionFee;
                return $totalFee;
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 最近の予約一覧を取得
     */
    public function recent()
    {
        $model = ExternalDatabaseService::getReservationModel();
        $shop = Shop::first();
        $reservations = $model::with(['member'])
            ->where('shop_id', $shop->id ?? 1)
            ->orderBy('date', 'desc')
            ->orderBy('time_start', 'desc')
            ->take(10)
            ->get()
            ->map(function($reservation) {
                try {
                    $memberName = $reservation->member ? Crypt::decryptString($reservation->member->getRawOriginal('name')) : '不明';
                } catch (\Exception $e) {
                    $memberName = $reservation->member ? $reservation->member->name : '不明';
                }

                // ローカルサーバーからitemとcourseの情報を取得
                $courseName = '';
                $castName = $reservation->item_name;

                if ($reservation->course_id) {
                    $course = \App\Models\Course::find($reservation->course_id);
                    $courseName = $course ? $course->title : '';
                }

                if ($reservation->item_id) {
                    $item = \App\Models\Item::find($reservation->item_id);
                    $castName = $item ? $item->item_name : $reservation->item_name;
                }

                return [
                    'id' => $reservation->id,
                    'datetime' => $reservation->date . ' ' . $reservation->time_start,
                    'member_name' => $memberName,
                    'course' => $courseName,
                    'cast_name' => $castName
                ];
            });

        return response()->json($reservations);
    }

    /**
     * 予約の詳細情報を取得
     */
    public function details($id)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $shop = Shop::first();
        $reservation = $model::where('shop_id', $shop->id ?? 1)->findOrFail($id);
        $reservation->load(['member', 'equipment']);

        // ローカルサーバーからitemとcourseの情報を取得
        if ($reservation->item_id) {
            $item = \App\Models\Item::find($reservation->item_id);
            $reservation->item = $item;
        }

        if ($reservation->course_id) {
            $course = \App\Models\Course::find($reservation->course_id);
            $reservation->course = $course;
        }

        try {
            $memberName = $reservation->member ? Crypt::decryptString($reservation->member->getRawOriginal('name')) : '不明';
            $memberEmail = $reservation->member ? Crypt::decryptString($reservation->member->getRawOriginal('email')) : '';
        } catch (\Exception $e) {
            $memberName = $reservation->member ? $reservation->member->name : '不明';
            $memberEmail = $reservation->member ? $reservation->member->email : '';
        }

        // オプション情報の取得
        $options = [];
        if ($reservation->option) {
            $optionIds = is_array($reservation->option) ? $reservation->option : json_decode($reservation->option, true);
            if ($optionIds) {
                $options = \App\Models\Option::whereIn('id', $optionIds)->pluck('title')->join('、');
            }
        }

        // 有料オプション情報の取得
        $optionPays = [];
        if ($reservation->option_pay) {
            $optionPayIds = is_array($reservation->option_pay) ? $reservation->option_pay : json_decode($reservation->option_pay, true);
            if ($optionPayIds) {
                $optionPays = \App\Models\OptionPay::whereIn('id', $optionPayIds)->pluck('title')->join('、');
            }
        }

        // コスチューム情報の取得
        $costumes = [];
        if ($reservation->costume) {
            $costumeIds = is_array($reservation->costume) ? $reservation->costume : json_decode($reservation->costume, true);
            if ($costumeIds) {
                $costumes = \App\Models\Costume::whereIn('id', $costumeIds)->pluck('name')->join('、');
            }
        }

        return view('admin.reservations.detail', compact('reservation', 'memberName', 'memberEmail', 'options', 'optionPays', 'costumes'));
    }
}
