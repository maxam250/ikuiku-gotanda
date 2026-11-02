<?php
// コースとコース時間を連動させて算出するタイプの予約フォームのコントローラー
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
use App\Models\Link;
use App\Services\ExternalDatabaseService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmation;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Auth;

class ReservationController_cast extends Controller
{
    /**
     * index
     */
    public function index(Request $request)
    {
        // ログインしていない場合は選択画面にリダイレクト
        if (!Auth::guard('member')->check() && !$request->session()->has('visitor_access')) {
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
        $links = Link::where('active', 1)
            ->orderBy('position', 'asc')
            ->orderBy('sort', 'asc')
            ->get();

        // セッションからの古い入力値を取得
        $oldInput = $request->session()->get('reservation_form_data', []);

        // ログインユーザーの場合、会員情報を設定
        if (Auth::guard('member')->check()) {
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
        $item = Item::with(['myOptions', 'myCostumes'])->findOrFail($itemId);

        return response()->json([
            'myOptions' => $item->myOptions,
            'myCostumes' => $item->myCostumes
        ]);
    }

    /**
     * confirm
     */
    public function confirm(Request $request)
    {
        //
        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();

        // ログインしていない場合のバリデーション
        if (!Auth::guard('member')->check() && !$request->session()->has('visitor_access')) {
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
        } else {
            // ログインしている場合のバリデーション
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'date' => 'required|date',
                'course_id' => 'required|integer',
                'course_time' => 'required|integer',
                // その他の必要なバリデーションルール
            ]);
        }

        //セッション
        $request->session()->put('reservation_form_data', $request->all());
// dd($request);
        $confirms = $request->all();
        $fullTel = ''; // 初期化

        // costume1～3の初期化
        $confirms['costume1'] = $confirms['costume1'] ?? null;
        $confirms['costume2'] = $confirms['costume2'] ?? null;
        $confirms['costume3'] = $confirms['costume3'] ?? null;
        // equipment_idの初期化
        $confirms['equipment_id'] = $confirms['equipment_id'] ?? null;

        if (!Auth::guard('member')->check() && !$request->session()->has('visitor_access')) {
            // 非会員の場合、telを結合
            $telParts = $request->input('tel');
            $fullTel = implode('-', array_filter($telParts)); // 空の要素を除外
            $confirms['tel'] = $fullTel;
        } else {
            // 会員の場合、会員情報から電話番号とメールアドレスを取得
            $member = Auth::guard('member')->user();
            $fullTel = $member->tel;
            $confirms['tel'] = $fullTel;
            // 暗号化されたメールアドレスを復号化
            $confirms['email'] = \Illuminate\Support\Facades\Crypt::decryptString($member->email);
        }

        //item_nameが空の時選択したitemの名前を挿入
        if ($confirms['item_name'] == null) {
            $get_item = Item::findOrFail($confirms['item_id']);
            $confirms['item_name'] = $get_item->item_name;
        }

        // セッションに保存する際は、結合した値を保存
        $request->session()->put('reservation_form_data', array_merge($request->all(), [
            'tel' => $fullTel,
            'email' => $confirms['email']
        ]));

        // time_endを挿入
        if (isset($confirms['time_start']) && isset($confirms['course_time'])) {
            $course_time_data = CourseFee::findOrFail($confirms['course_time']);
            $course_time = intval($course_time_data->course_time); // 整数値に変換

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
        } else {
            $confirms['time_end'] = null;
        }

        return view('reservation.confirm', compact('confirms', 'items', 'courses', 'course_fees', 'options', 'option_pays', 'equipments', 'costumes'));
    }

    /**
     * store
     */
    public function store(Request $request)
    {
        $rules = [
            'date' => 'required|date|after:today',
            'time_start' => 'required',
            'course_id' => 'required',
            'course_time' => 'required',
            'comment' => 'nullable|string|max:1000',
            'fee_course' => 'required|integer',
            'fee_option' => 'required|integer',
            'fee_myoption' => 'required|integer',
            'fee_mycostume' => 'required|integer',
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

        $data = $request->all();

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

        // mycostumeフィールドが配列の場合、JSON文字列に変換
        if(isset($data['mycostume'])){
            if (is_array($data['mycostume'])) {
                $data['mycostume'] = json_encode($data['mycostume']);
            }
        }else{
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

        // 料金情報を設定
        $data['fee_course'] = $request->input('fee_course', 0);
        $data['fee_option'] = $request->input('fee_option', 0);
        $data['fee_myoption'] = $request->input('fee_myoption', 0);
        $data['fee_mycostume'] = $request->input('fee_mycostume', 0);
        $data['fee_total'] = $request->input('fee_total', 0);

        // shop_idを設定
        $data['shop_id'] = 3;
        $reservation = ExternalDatabaseService::createReservation($data);

        // shopテーブルからメールアドレスを取得
        $shopEmail = Shop::value('shop_mail') ?? config('mail.from.address');

        // メール送信
        // ユーザーへ自動返信（会員の場合は会員情報のメールアドレスを復号化して使用）
        $userEmail = Auth::guard('member')->check() ?
            \Illuminate\Support\Facades\Crypt::decryptString(Auth::guard('member')->user()->email) :
            $reservation->email;

        Mail::to($userEmail)->send(new ReservationConfirmation($reservation, $shopEmail, false));

        // ショップへ自動送信
        Mail::to($shopEmail)->send(new ReservationConfirmation($reservation, $userEmail, true));

        return redirect()->route('reservation.thanks')->with('success', '予約が完了しました');
    }


    /**
     * thanks
     */
    public function thanks()
    {
        return view('reservation.thanks');
    }

    public function select(Request $request)
    {
        // ビジターとしてアクセスした場合のセッション設定
        if ($request->has('type') && $request->type === 'visitor') {
            $request->session()->put('visitor_access', true);
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
}
