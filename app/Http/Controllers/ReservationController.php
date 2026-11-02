<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\OptionCate;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\CourseCate;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\Shop;
use App\Models\Equipment;
use App\Models\Costume;
use App\Models\CostumeCate;
use App\Models\Link;
use App\Models\Setting;
use App\Services\ExternalDatabaseService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmation;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class ReservationController extends Controller
{
    /**
     * index
     */
    public function index(Request $request)
    {
        $isMember = Auth::guard('member')->check();
        $isVisitor = $request->session()->get('visitor_access', false);
        $shops = Shop::first();
        // デバッグログを追加
        // \Log::debug('index method - member auth:', ['auth' => $isMember]);
        // \Log::debug('index method - visitor_access session:', ['session' => $isVisitor]);

        // ログインしていない場合は選択画面にリダイレクト
        if (!$isMember && !$isVisitor) {
            return redirect()->route('reservation.select');
        }

        // 必要なデータを取得
        $items = Item::where('item_active', 1)
                    ->orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();
        $costumeCates = CostumeCate::where('active', 1)
                                    ->orderBy('sort', 'asc')
                                    ->with(['costumes' => function($query) {
                                        $query->where('active', 1)->orderBy('name', 'asc');
                                    }])
                                    ->get();
        $links = Link::where('active', 1)
            ->orderBy('position', 'asc')
            ->orderBy('sort', 'asc')
            ->get();

        // セッションからの古い入力値を取得
        $oldInput = $request->session()->get('reservation_form_data', []);

        // セッションから初期データを取得
        $initialData = $request->session()->get('reservation_initial_data', []);

        // 初期データがある場合はoldInputにマージ
        if (!empty($initialData)) {
            $oldInput = array_merge($oldInput, $initialData);
            // 使用後は削除
            $request->session()->forget('reservation_initial_data');
        }

        // 会員の場合、会員情報を設定
        if ($isMember) {
            $member = Auth::guard('member')->user();
            $oldInput = array_merge($oldInput, [
                'name' => $member->name,
                'email' => $member->email,
                'tel' => explode('-', $member->tel)
            ]);
        }


        return view('reservation.index', compact(
            'items',
            'options',
            'option_pays',
            'courses',
            'course_fees',
            'oldInput',
            'equipments',
            'costumes',
            'costumeCates',
            'links'
        ));
    }

    /**
     * getCourseFees
     * Ajaxでコースと連動して料金を表示
     * @return option
     */
    public function getCourseFees($courseId)
    {
        $courseFees = CourseFee::where('course_id', $courseId)->get();
        return response()->json($courseFees);
    }

    /**
     * getCastItems
     * キャストの専用オプションとコスチュームを取得
     */
    public function getCastItems($itemId)
    {
        // Item モデルは myoptions / mycostumes（小文字）で定義。明示的に同じ実体を JSON で返す
        $item = Item::with(['myoptions', 'mycostumes'])->findOrFail($itemId);

        return response()->json([
            'myOptions' => $item->myoptions,
            'myCostumes' => $item->mycostumes,
        ]);
    }

    /**
     * confirm
     */
    public function confirm(Request $request)
    {
        \Log::info('Confirm method called', [
            'method' => $request->method(),
            'url' => $request->url(),
            'session' => session()->all()
        ]);

        // store 失敗時の redirect()->back() などで GET /reservation/confirm に来た場合はセッションから再表示
        $fromSession = $request->isMethod('get');
        if ($fromSession) {
            $sessionData = $request->session()->get('reservation_form_data');
            if (empty($sessionData)) {
                return redirect()->route('reservation.index')->with('error', '予約情報が見つかりません。最初からやり直してください。');
            }
            $request->merge($sessionData);
        }

        // 過去の日付を選択した場合のバリデーション（入力フォームからの POST のみ厳密にチェック）
        if (!$fromSession) {
            $selectedDate = $request->input('date');
            $today = now()->format('Y-m-d');

            if ($selectedDate < $today) {
                return redirect()->back()->withErrors(['date' => '過去の日付は選択できません。今日以降の日付を選択してください。'])->withInput();
            }
        }

        $isMember = Auth::guard('member')->check();
        $isVisitor = $request->session()->get('visitor_access', false);

        // ビジター／会員のバリデーション（確認画面の GET 再表示時はセッション済みのためスキップ）
        if (!$fromSession) {
            if ($isVisitor) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'nullable|email',
                    'tel.*' => 'nullable|numeric|digits_between:3,4',
                    'tel' => 'nullable|array|size:3',
                    'date' => 'required|date',
                    'course_id' => 'required|integer',
                    'course_time' => 'required|integer',
                    // その他の必要なバリデーションルール
                ]);
            } elseif ($isMember) {
                // 会員の場合のバリデーション
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'date' => 'required|date',
                    'course_id' => 'required|integer',
                    'course_time' => 'required|integer',
                    // その他の必要なバリデーションルール
                ]);
            } else {
                // どちらでもない場合はエラー
                return redirect()->route('reservation.index')->with('error', 'もう一度入力してください');
            }
        }

        //セッション
        $request->session()->put('reservation_form_data', $request->all());
// dd($request);
        $confirms = $request->all();
        // マイコスチュームは単一選択（スカラー）でも配列として扱う
        if (isset($confirms['mycostume']) && $confirms['mycostume'] !== '' && $confirms['mycostume'] !== null && !is_array($confirms['mycostume'])) {
            $confirms['mycostume'] = [$confirms['mycostume']];
        }
        $fullTel = ''; // 初期化

        // costume1～3の初期化
        $confirms['costume1'] = $confirms['costume1'] ?? null;
        $confirms['costume2'] = $confirms['costume2'] ?? null;
        $confirms['costume3'] = $confirms['costume3'] ?? null;
        // equipment_idの初期化
        $confirms['equipment_id'] = $confirms['equipment_id'] ?? null;

        // ビジターの場合のバリデーション

        if ($isMember) {
            // 会員の場合、会員情報から電話番号とメールアドレスを取得
            $member = Auth::guard('member')->user();
            $fullTel = $member->tel;
            $confirms['tel'] = $fullTel;
            // 暗号化されたメールアドレスを復号化
            $confirms['email'] = \Illuminate\Support\Facades\Crypt::decryptString($member->email);
        } elseif ($isVisitor) {
            // 非会員（ビジター）の場合、telを結合（セッション再表示時は既に文字列）
            $telParts = $request->input('tel');
            $fullTel = is_array($telParts)
                ? implode('-', array_filter($telParts))
                : (string) $telParts;
            $confirms['tel'] = $fullTel;
            // ビジターの場合、emailフィールドを設定
            $confirms['email'] = $request->input('email');
        } else {
            // どちらでもない場合はエラー
            return redirect()->route('reservation.index')->with('error', 'もう一度入力してください');
        }

        //item_nameが空の時選択したitemの名前を挿入
        // if ($confirms['item_name'] == null && !empty($confirms['item_id'])) {
        //     try {
        //         $get_item = Item::findOrFail($confirms['item_id']);
        //         $confirms['item_name'] = $get_item->item_name;
        //     } catch (\Exception $e) {
        //         $confirms['item_name'] = '指名なし';
        //     }
        // } elseif (empty($confirms['item_id']) || empty($confirms['item_name'])) {
        //     $confirms['item_name'] = '指名なし';
        // }

        // セッションに保存する際は、結合した値を保存
        $sessionData = array_merge($request->all(), [
            'tel' => $fullTel,
        ]);

        if ($isMember) {
            $sessionData['email'] = $confirms['email'];
        }

                $request->session()->put('reservation_form_data', $sessionData);

        // time_endを挿入
        if (isset($confirms['time_start']) && isset($confirms['course_time'])) {
            try {
                // course_timeが直接の分数の場合の処理
                $course_time = intval($confirms['course_time']); // 直接の分数を整数値に変換

                $start_time = DateTime::createFromFormat('H:i:s', $confirms['time_start']);

                if ($start_time !== false) { // 正しく解析されたか確認
                    // DateInterval を使用して時間を加算
                    $interval = new DateInterval("PT{$course_time}M");
                    $end_time = clone $start_time;
                    $end_time->add($interval);

                    $confirms['time_end'] = $end_time->format('H:i:s');
                } else {
                    $confirms['time_end'] = null; // 開始時間が無効な場合
                }
            } catch (\Exception $e) {
                $confirms['time_end'] = null;
            }
        } else {
            $confirms['time_end'] = null;
        }

        // 料金制設定を取得
        $pricingType = Setting::firstOrCreate([])->pricing_type ?? 'unified';

        // 料金計算
        $courseFee = 0;
        if (!empty($confirms['item_id'])) {
            $courseFee = $this->calculateCourseFee($confirms['item_id'], $confirms['course_id'], $confirms['course_time'], $pricingType);
        }

        // ビューに渡すデータを取得（料金計算の前に定義）
        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();

        // オプション料金の計算
        $optionFee = 0;
        if (isset($confirms['option_pay'])) {
            foreach ($option_pays as $option_pay) {
                if (in_array($option_pay->id, (array)$confirms['option_pay'])) {
                    $optionFee += $option_pay->price;
                }
            }
        }

        // マイオプション料金の計算
        $myoptionFee = 0;
        if (isset($confirms['myoption']) && !empty($confirms['item_id'])) {
            foreach ($items as $item) {
                if ($item->id == $confirms['item_id']) {
                    foreach ($item->myOptions as $myOption) {
                        if (in_array($myOption->id, (array)$confirms['myoption'])) {
                            $myoptionFee += $myOption->price;
                        }
                    }
                }
            }
        }

        // マイコスチューム料金の計算
        $mycostumeFee = 0;
        if (isset($confirms['mycostume']) && !empty($confirms['item_id'])) {
            foreach ($items as $item) {
                if ($item->id == $confirms['item_id']) {
                    foreach ($item->myCostumes as $myCostume) {
                        if (in_array($myCostume->id, (array)$confirms['mycostume'])) {
                            $mycostumeFee += $myCostume->price;
                        }
                    }
                }
            }
        }

        // 小計と消費税、合計の計算
        $subtotal = $courseFee + $optionFee + $myoptionFee + $mycostumeFee;
        $tax = floor($subtotal * 0.1);
        $total = $subtotal + $tax;

        return view('reservation.confirm', compact('confirms', 'items', 'courses', 'course_fees', 'options', 'option_pays', 'equipments', 'costumes', 'courseFee', 'optionFee', 'myoptionFee', 'mycostumeFee', 'total'));
    }

    /**
     * store
     */
        public function store(Request $request)
    {
        try {
            \Log::info('Reservation store method called', ['request_data' => $request->all()]);

            $rules = [
                'date' => 'required|date|after_or_equal:today',
                'time_start' => 'required',
                'course_id' => 'required',
                'course_time' => 'required',
                'comment' => 'nullable|string|max:1000',
                'fee_course' => 'required|integer',
                'fee_option' => 'required|integer',
                'fee_myoption' => 'required|integer',
                'fee_mycostume' => 'required|integer',
                'fee_adjust' => 'nullable|integer',
                'fee_total' => 'required|integer',
            ];

            // ビジター用のバリデーションルール
            if (!Auth::guard('member')->check()) {
                $rules = array_merge($rules, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'tel' => 'required|string|max:20',
                ]);
            }

            $validated = $request->validate($rules);
            \Log::info('Validation passed');

            $data = $request->all();

            // キャスト名 item_nameを取得
            $item = Item::find($data['item_id']);
            $data['item_name'] = $item ? $item->item_name : '未登録';

            // equipment_idのexternal_id設定
            if (isset($data['equipment_id']) && $data['equipment_id']) {
                $equipment = Equipment::find($data['equipment_id']);
                if ($equipment && $equipment->external_id) {
                    $data['equipment_id'] = $equipment->external_id;
                }
            }

            // コスチューム関連の配列をJSON形式で保存
            $costumes = [];
            if (!empty($data['costume1'])) $costumes[] = $data['costume1'];
            if (!empty($data['costume2'])) $costumes[] = $data['costume2'];
            if (!empty($data['costume3'])) $costumes[] = $data['costume3'];
            $data['costume'] = !empty($costumes) ? json_encode($costumes) : null;

            // 個別のcostume1～3は削除（JSON形式の配列に統合したため）
            unset($data['costume1']);
            unset($data['costume2']);
            unset($data['costume3']);

            // optionフィールドが配列の場合、JSON文字列に変換
            if(isset($data['option'])){
                if (is_array($data['option'])) {
                    $data['option'] = json_encode($data['option']);
                }
            }else{
                $data['option'] = null;
            }

            // option_payフィールドが配列の場合、JSON文字列に変換
            if(isset($data['option_pay'])){
                if (is_array($data['option_pay'])) {
                    $data['option_pay'] = json_encode($data['option_pay']);
                }
            }else{
                $data['option_pay'] = null;
            }

            // myoptionフィールドが配列の場合、JSON文字列に変換
            if(isset($data['myoption'])){
                if (is_array($data['myoption'])) {
                    $data['myoption'] = json_encode($data['myoption']);
                }
            }else{
                $data['myoption'] = null;
            }

            // mycostumeフィールドをJSON文字列に変換（単一ID・配列の両方）
            if (isset($data['mycostume']) && $data['mycostume'] !== '' && $data['mycostume'] !== null) {
                if (is_array($data['mycostume'])) {
                    $data['mycostume'] = json_encode(array_values($data['mycostume']));
                } else {
                    $data['mycostume'] = json_encode([(int) $data['mycostume']]);
                }
            } else {
                $data['mycostume'] = null;
            }

            if (Auth::guard('member')->check()) {
                $member = Auth::guard('member')->user();
                $data['member_id'] = $member->id;
                $data['name'] = $member->name;
                // 会員の場合はemail、telは保存しない（member_idとの紐付けで十分）
                unset($data['email']);
                unset($data['tel']);
            }

            // 料金情報を設定（fee_total は税抜合計 = 各料金 + fee_adjust）
            $data['fee_course']   = (int) $request->input('fee_course', 0);
            $data['fee_option']   = (int) $request->input('fee_option', 0);
            $data['fee_myoption'] = (int) $request->input('fee_myoption', 0);
            $data['fee_mycostume'] = (int) $request->input('fee_mycostume', 0);
            $data['fee_adjust']   = (int) $request->input('fee_adjust', 0);
            $data['fee_total']    = $data['fee_course']
                                  + $data['fee_option']
                                  + $data['fee_myoption']
                                  + $data['fee_mycostume']
                                  + $data['fee_adjust'];

            // shop_id は reservations.shop_id → shops.id（整数）。shop_idname は文字列のため不可
            $shops = Shop::first();
            $data['shop_id'] = $shops->id ?? 1;

            // 外部DBに保存
            try {
                $reservation = ExternalDatabaseService::createReservation($data);
                \Log::info('Reservation created successfully', ['reservation_id' => $reservation->id]);
            } catch (\Exception $e) {
                \Log::error('Error creating reservation:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                throw $e;
            }

            // shopテーブルからメールアドレスを取得
            $shopEmail = Shop::value('shop_mail') ?? config('mail.from.address');

            // メール送信処理
            try {
                $userEmail = Auth::guard('member')->check() ?
                    \Illuminate\Support\Facades\Crypt::decryptString(Auth::guard('member')->user()->email) :
                    $reservation->email;

                Mail::to($userEmail)->send(new ReservationConfirmation($reservation, $shopEmail, false));
                Mail::to($shopEmail)->send(new ReservationConfirmation($reservation, $userEmail, true));
                \Log::info('Emails sent successfully');
            } catch (\Exception $e) {
                \Log::error('Email sending failed:', ['error' => $e->getMessage()]);
                // メール送信に失敗しても予約は完了させる
            }

            \Log::info('Redirecting to thanks page');

            // リダイレクト前にセッションにフラグを設定
            session(['reservation_completed' => true]);

            // フォームデータをクリアして再送信を防ぐ
            session()->forget('reservation_form_data');

            return redirect()->route('reservation.thanks')->with('success', '予約が完了しました');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Store method error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()->withErrors(['error' => '予約の処理中にエラーが発生しました。時間をおいて再度お試しください。'])->withInput();
        }
    }


    /**
     * thanks
     */
    public function thanks()
    {
        \Log::info('Thanks page accessed', ['session' => session()->all()]);
        return view('reservation.thanks');
    }

    public function select(Request $request)
    {
        // URLパラメータから初期値を取得
        $itemId = $request->get('item_id');
        $date = $request->get('date');

        // 初期値をセッションに保存
        if ($itemId || $date) {
            $initialData = [];
            if ($itemId) $initialData['item_id'] = $itemId;
            if ($date) $initialData['date'] = $date;
            $request->session()->put('reservation_initial_data', $initialData);
        }

        // 会員が既にログインしている場合は直接予約ページにリダイレクト
        if (Auth::guard('member')->check()) {
            return redirect()->route('reservation.index');
        }

        // デバッグログを追加
        // \Log::debug('select method - request type:', ['type' => $request->get('type')]);

        // ビジターとしてアクセスした場合のセッション設定
        if ($request->has('type') && $request->type === 'visitor') {
            $request->session()->put('visitor_access', true);
            // \Log::debug('select method - visitor_access session set:', ['session' => $request->session()->get('visitor_access')]);
            return redirect()->route('reservation.index');
        }

        return view('reservation.select');
    }

    public function form(Request $request)
    {

        $items = Item::where('item_active', 1)
                    ->orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();
// dd($courses);
        return view('reservation.form', compact(
            'items',
            'options',
            'option_pays',
            'courses',
            'course_fees',
            'costumes'
        ));
    }

    public function complete()
    {
        return view('reservation.complete');
    }

    /**
     * 料金計算
     * 統一料金制: course_feesテーブルから course_id + course_time で取得
     * 個別料金制: item_course_feesテーブルからキャスト個別料金を取得
     */
    private function calculateCourseFee($itemId, $courseId, $courseTime, $pricingType = 'unified')
    {
        try {
            // ── 統一料金制 ──────────────────────────────────────────
            if ($pricingType === 'unified') {
                $courseFeeRow = CourseFee::where('course_id', $courseId)
                    ->where('course_time', $courseTime)
                    ->first();

                return $courseFeeRow ? (int) $courseFeeRow->fee : 0;
            }

            // ── 個別料金制 ──────────────────────────────────────────
            $feeSetting = \DB::table('item_course_fees')
                ->where('item_id', $itemId)
                ->where('course_id', $courseId)
                ->first();

            if (!$feeSetting) {
                return 0;
            }

            // 一律料金を使用する場合
            if ($feeSetting->use_uniform_rate && $feeSetting->uniform_rate) {
                return (int) ($feeSetting->uniform_rate * ($courseTime / 30));
            }

            // 180分までは個別料金を使用
            if ($courseTime <= 180) {
                $feeColumn = 'fee_' . $courseTime;
                if (property_exists($feeSetting, $feeColumn) && $feeSetting->$feeColumn !== null) {
                    return (int) $feeSetting->$feeColumn;
                }
            }

            // 210分以上は延長料金を上乗せ
            if ($courseTime >= 210) {
                $baseFee = (property_exists($feeSetting, 'fee_180') && $feeSetting->fee_180 !== null)
                    ? (int) $feeSetting->fee_180
                    : 0;
                $extensionUnits = ceil(($courseTime - 180) / 30);
                $extensionFee = ($feeSetting->fee_extension !== null)
                    ? (int) ($feeSetting->fee_extension * $extensionUnits)
                    : 0;
                return $baseFee + $extensionFee;
            }

            return 0;

        } catch (\Exception $e) {
            return 0;
        }
    }

}
