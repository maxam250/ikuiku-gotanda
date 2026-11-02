<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\Profile;
use App\Models\IconItem;
use App\Models\Interview;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Fanclub;
use App\Models\FanclubContent;
use App\Models\Setting;
use App\Models\Myoption;
use App\Models\MyoptionCate;
use App\Models\Mycostume;
use App\Models\MycostumeCate;
use App\Models\PremiumRank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\Rule;//loginidに対してユニーク制約のバリデーション用


class ItemController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $tab    = in_array($request->input('tab'), ['all', 'active', 'passive'])
                    ? $request->input('tab')
                    : 'active';

        // 検索条件の基底クエリ
        $baseQuery = Item::query();
        if ($search !== '') {
            $baseQuery->where('item_name', 'like', '%' . $search . '%');
        }

        // 各タブのカウント（検索条件を反映）
        $totalCount   = (clone $baseQuery)->count();
        $activeCount  = (clone $baseQuery)->where('item_active', 1)->count();
        $passiveCount = (clone $baseQuery)->where('item_active', '!=', 1)->count();

        // タブに応じてフィルタリング
        $listQuery = clone $baseQuery;
        if ($tab === 'active') {
            $listQuery->where('item_active', 1);
        } elseif ($tab === 'passive') {
            $listQuery->where('item_active', '!=', 1);
        }

        $items = $listQuery->with('thumbnail')
            ->when($tab === 'all', fn($q) => $q->orderByDesc('item_active'))
            ->orderByRaw('COALESCE(item_sort, 999999) ASC')
            ->orderBy('item_sort', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(200)
            ->appends(['search' => $search, 'tab' => $tab]);

        return view('admin.items.index', compact('items', 'totalCount', 'activeCount', 'passiveCount', 'search', 'tab'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    public function store(Request $request)
    {

        // dd($request);
        // バリデーション
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            // 他の必要なフィールドも追加
        ]);

        // 既存キャストのitem_sortを全て+1してから先頭に追加
        Item::query()->increment('item_sort');

        // 新しいキャストを作成
        $item = new Item();
        $item->item_name = $validated['item_name'];
        $item->item_sort = 1;

        $item->save(); // データベースに保存

        // 成功したらリダイレクト
        return redirect()->route('admin.items.edit', $item->id)->with('success', 'キャストが追加されました');

    }



    public function edit(Item $item )
    {

        $item_id = $item->id;


        // 画像リストを取得
        $images = ItemImage::where('item_id', $item_id)->orderby('sort', 'asc')->get();

        // オプションリストを取得
        $options = Option::orderBy('sort', 'asc')->get();
        // 選択されたオプションをビューに渡す
        $selectedOptions = $item->options->pluck('id')->toArray();

        // オプションPAYリストを取得
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        // 選択されたオプションをビューに渡す
        $selectedOptionPays = $item->option_pays->pluck('id')->toArray();

        // プロフィールリストを取得
        $profiles = Profile::orderBy('sort', 'asc')->get();
        // 選択されたオプションをビューに渡す
        $profileContents = [];
        foreach($item->profiles as $profile){
            $profileContents[$profile->id] = $profile->pivot->content;
        }
        // $selectedProfiles = $item->profiles->pluck('id')->toArray();

        // キャストアイコンリストを取得
        $icon_items = IconItem::orderBy('sort', 'asc')->get();
        // 選択されたオプションをビューに渡す
        $selectedIconItems = $item->icon_items->pluck('id')->toArray();

        // インタビューリストを取得
        $interviews = Interview::orderBy('sort', 'asc')->get();
        // 選択されたインタビューをビューに渡す
        $interviewContents = [];
        foreach($item->interviews as $interview){
            $interviewContents[$interview->id] = $interview->pivot->content;
        }

        // コースリストを取得
        $courses = Course::orderBy('sort', 'asc')->get();
        // 選択されたコースとそのステータスをビューに渡す
        $courseStatuses = [];
        foreach($item->courses as $course){
            $courseStatuses[$course->id] = $course->pivot->status;
        }

        // コース料金設定を取得
        $courseFeeSettings = [];
        foreach($item->courseFees as $courseFee){
            $courseFeeSettings[$courseFee->id] = [
                'fee_60' => $courseFee->pivot->fee_60,
                'fee_90' => $courseFee->pivot->fee_90,
                'fee_120' => $courseFee->pivot->fee_120,
                'fee_150' => $courseFee->pivot->fee_150,
                'fee_180' => $courseFee->pivot->fee_180,
                'fee_extension' => $courseFee->pivot->fee_extension,
                'uniform_rate' => $courseFee->pivot->uniform_rate,
                'use_uniform_rate' => $courseFee->pivot->use_uniform_rate,
            ];
        }

        // クーポンリストを取得
        $coupons = Coupon::active()->valid()->orderBy('title', 'asc')->get();
        // 選択されたクーポンをビューに渡す
        $selectedCoupons = $item->coupons->pluck('id')->toArray();

// dd($option_pays);


        // dd( $selectedOptions);

        // ファンクラブ情報
        $fanclub = Fanclub::where('cast_id', $item->id)
            ->withCount(['memberships', 'memberships as active_memberships_count' => fn ($q) => $q->where('status', 'active')])
            ->first();

        // ファンクラブコンテンツ（最新20件）
        $fanclubContents = FanclubContent::where('cast_id', $item->id)
            ->withCount('children')
            ->latest()
            ->take(20)
            ->get();

        $setting = Setting::firstOrCreate([]);

        // マイコスチューム・マイオプション
        $mycostumes     = Mycostume::where('item_id', $item_id)->with('category')->orderBy('sort')->get();
        $myoptions      = Myoption::where('item_id', $item_id)->with('category')->orderBy('sort')->get();
        $mycostumeCates = MycostumeCate::orderBy('sort')->get();
        $myoptionCates  = MyoptionCate::orderBy('sort')->get();

        // プレミアランクリストを取得
        $premiumRanks = PremiumRank::orderBy('sort', 'asc')->get();

        return view('admin.items.edit', compact('item', 'images','options', 'selectedOptions','option_pays', 'selectedOptionPays','profiles', 'profileContents','icon_items', 'selectedIconItems','interviews', 'interviewContents','courses', 'courseStatuses', 'courseFeeSettings', 'coupons', 'selectedCoupons', 'fanclub', 'fanclubContents', 'setting', 'mycostumes', 'myoptions', 'mycostumeCates', 'myoptionCates', 'premiumRanks'));
    }

    // 更新するカラムのリストを設定

    public function update(Request $request, $id)
    {

        //　バリデータ
        $request->validate([
            'item_name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('items', 'email')->ignore($id),
            ],
            'password' => 'nullable|string|min:4',
            'loginid' => [
                'nullable',
                'string',
                Rule::unique('items', 'loginid')->ignore($id),
            ],
            'default_sch_start' => ['nullable', 'regex:/^\d{2}:\d{2}:\d{2}$/'],
            'default_sch_end' => ['nullable', 'regex:/^\d{2}:\d{2}:\d{2}$/'],
        ]);

        // 項目の取得
        $item = Item::findOrFail($id);


        // フォームからのデータをユーザーに一括で代入
        $item->fill($request->except('password', 'item_active', 'item_p_comment_date', 'item_sort'));

        // パスワードの暗号化と更新
        // $item->email = $request->input('email');

        if ($request->filled('password')) {
            $item->password       = Hash::make($request->input('password'));
            $item->item_pass_text = $request->input('password');
        }

        // 公開チェック（未チェック時も値を送る hidden とセット。無いフォームでは既存値を維持）
        if ($request->has('item_active')) {
            $item->item_active = $request->boolean('item_active') ? 1 : 0;
        }

        // プレミアランク（空文字列はNULLに変換）
        $item->premium_rank_id = $request->filled('premium_rank_id') ? (int)$request->input('premium_rank_id') : null;

        // リアルタイムコメント投稿時間
        $item_p_comment_date = date('Y-m-d H:i:s');
        $item->item_p_comment_date = $item_p_comment_date;



        // オプションの関連付け更新　→Ajaxにしているため除外
        //$item->options()->sync($request->input('options', []));
        // オプションPAYの関連付け更新　→Ajaxにしているため除外
        //$item->option_pays()->sync($request->input('option_pays', []));
        $item->save();


        return redirect()->route('admin.items.edit',['item' => $id])->with('success', 'アップデート成功');

        //var_dump($request);
    }


    public function updateLogin(Request $request, $id)
    {
        $request->validate([
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('items', 'email')->ignore($id),
            ],
            'password' => 'nullable|string|min:4',
            'loginid' => [
                'nullable',
                'string',
                Rule::unique('items', 'loginid')->ignore($id),
            ],
            'item_line' => 'nullable|string|max:255',
        ]);

        $item = Item::findOrFail($id);
        $item->email = $request->input('email');
        $item->item_tel = $request->input('item_tel');
        $item->item_line = $request->input('item_line');
        $item->loginid = $request->input('loginid');

        if ($request->filled('password')) {
            $item->password = Hash::make($request->input('password'));
        }

        $item->save();

        return redirect()->route('admin.items.edit', ['item' => $id])->with('success', 'ログイン情報を更新しました');
    }


    //sort
    public function sort(Request $request)
    {
        $order = $request->input('order', []);

        if (! is_array($order)) {
            return response()->json(['success' => false, 'message' => 'Invalid order data'], 422);
        }

        foreach ($order as $item) {
            if (! isset($item['id'], $item['item_sort'])) {
                continue;
            }
            Item::where('id', $item['id'])->update(['item_sort' => (int) $item['item_sort']]);
        }

        return response()->json(['success' => true]);
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $item_id)
    {
        $item = Item::findOrFail($item_id);

        // 関連する画像を削除
        $images = ItemImage::where('item_id', $item_id)->get();
        foreach ($images as $image) {
            // ストレージからファイルを削除
            if (Storage::exists($image->filename)) {
                Storage::delete($image->filename);
            }
            $image->delete();
        }

        // items_* テーブルのレコードを削除
        \DB::table('item_option')->where('item_id', $item_id)->delete();
        \DB::table('item_option_pay')->where('item_id', $item_id)->delete();
        \DB::table('item_profile')->where('item_id', $item_id)->delete();
        \DB::table('item_icon_item')->where('item_id', $item_id)->delete();
        \DB::table('item_rank')->where('item_id', $item_id)->delete();
        \DB::table('item_interview')->where('item_id', $item_id)->delete();
        \DB::table('item_course')->where('item_id', $item_id)->delete();
        \DB::table('item_course_fees')->where('item_id', $item_id)->delete();

        // schedules テーブルのレコードを削除
        \DB::table('schedules')->where('item_id', $item_id)->delete();

        // mycostumes テーブルのレコードを削除（関連する画像も削除）
        $mycostumes = \DB::table('mycostumes')->where('item_id', $item_id)->get();
        foreach ($mycostumes as $mycostume) {
            if (isset($mycostume->filename) && Storage::exists($mycostume->filename)) {
                Storage::delete($mycostume->filename);
            }
        }
        \DB::table('mycostumes')->where('item_id', $item_id)->delete();

        // myoptions テーブルのレコードを削除（関連する画像も削除）
        $myoptions = \DB::table('myoptions')->where('item_id', $item_id)->get();
        foreach ($myoptions as $myoption) {
            if (isset($myoption->filename) && Storage::exists($myoption->filename)) {
                Storage::delete($myoption->filename);
            }
        }
        \DB::table('myoptions')->where('item_id', $item_id)->delete();

        // キャストを削除
        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'キャストが削除されました');
    }


    /**
     * 公開・非公開トグル
     */
    public function toggleActive(Item $item)
    {
        $item->update(['item_active' => $item->item_active ? 0 : 1]);
        return back();
    }


    public function sortImage($item_id)
    {
        // 必要なデータを取得
        // $item = Item::findOrFail($item_id);
        $images = ItemImage::where('item_id', $item_id)->get();

        // ビューにデータを渡して表示
        return view('admin.items.sort', [
            // 'item' => $item,
            'images' => $images,
            'url' => route('admin.items.sort', ['id' => $item_id])
        ]);
    }



    // オプション　Ajax
    public function updateOption(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        // dd($item);
        // リクエストからオプションのIDを取得
        $optionId = $request->input('option_id');
        $checked = $request->input('checked');  // trueなら選択、falseなら解除


 // デバッグ用にログに記録して確認
//  \Log::info('Option ID: ' . $optionId);
//  \Log::info('Checked status: ' . $checked);


        if ($checked === 'true' || $checked === true) {
            // チェックされた場合、関連付けを追加
            $item->options()->attach($optionId);
        } else {
            // チェックが外された場合、関連付けを削除
            $item->options()->detach($optionId);
        }

        return response()->json(['success' => true]);
    }



    // オプションPAY　Ajax
    public function updateOptionPay(Request $request, $id)
    {

        $item = Item::findOrFail($id);
        // リクエストからオプションのIDを取得
        $optionPayId = $request->input('option_pay_id');
        $checked = $request->input('pay_checked');  // trueなら選択、falseなら解除


        if ($checked === 'true' || $checked === true) {
            // チェックされた場合、関連付けを追加
            $item->option_pays()->attach($optionPayId);


        } else {
            // チェックが外された場合、関連付けを削除
            $item->option_pays()->detach($optionPayId);
        }

        return response()->json(['success' => true]);
    }


    // キャストアイコン Ajax
    public function updateIconItem(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        // リクエストからオプションのIDを取得
        $IconItemId = $request->input('iconItem_id');
        $checked = $request->input('iconItem_checked');  // trueなら選択、falseなら解除


        if ($checked === 'true' || $checked === true) {
            // チェックされた場合、関連付けを追加
            $item->icon_items()->attach($IconItemId);


        } else {
            // チェックが外された場合、関連付けを削除
            $item->icon_items()->detach($IconItemId);
        }

        return response()->json(['success' => true]);
    }


    //プロフィール
    public function updateProfile(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $profiles = $request->input('profiles', []);
        $pivotData = [];

        // プロフィールの内容を更新
        foreach ($profiles as $profileId => $content) {
            $pivotData[$profileId] = ['content' => $content];
        }

        // 中間テーブルを更新 or 新規追加（detachはしない）
        $item->profiles()->syncWithoutDetaching($pivotData);

        return redirect()->route('admin.items.edit', $id)->with('success', 'プロフィールが更新されました');
    }

    //Q&A
    public function updateInterview(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $interviews = $request->input('interviews', []);
        $pivotData = [];

        // Q&Aの内容を更新
        foreach ($interviews as $interviewId => $content) {
            $pivotData[$interviewId] = ['content' => $content];
        }

        // 中間テーブルを更新 or 新規追加（detachはしない）
        $item->interviews()->syncWithoutDetaching($pivotData);

        return redirect()->route('admin.items.edit', $id)->with('success', 'Q&Aが更新されました');
    }


    // プロフィール　Ajax
    // public function updateProfile(Request $request, $id)
    // {

    //     $item = Item::findOrFail($id);
    //     // リクエストからオプションのIDを取得
    //     $ptorileId = $request->input('profile_id');
    //     $checked = $request->input('pay_checked');  // trueなら選択、falseなら解除


    //     if ($checked === 'true' || $checked === true) {
    //         // チェックされた場合、関連付けを追加
    //         $item->profiles()->attach($optionPayId);


    //     } else {
    //         // チェックが外された場合、関連付けを削除
    //         $item->profiles()->detach($optionPayId);
    //     }

    //     return response()->json(['success' => true]);
    // }


/*
バリデーションしたいカラムのみ設定


基本的なバリデーションルール
required: このフィールドは必須です。
nullable: このフィールドは空でも許可されます。
string: このフィールドは文字列でなければなりません。
integer: このフィールドは整数でなければなりません。
numeric: このフィールドは数値でなければなりません。
email: このフィールドは有効なメールアドレス形式でなければなりません。
url: このフィールドは有効なURL形式でなければなりません。
boolean: このフィールドは true または false でなければなりません。
サイズバリデーション
max
: フィールドの最大値、文字列の場合は最大文字数。
min
: フィールドの最小値、文字列の場合は最小文字数。
between
,max: フィールドの値が指定した範囲内でなければなりません。
size
: フィールドの値が指定されたサイズでなければなりません。
日付バリデーション
date: このフィールドは有効な日付でなければなりません。
date_format
: フィールドの日付が指定した形式に従っている必要があります。
after
: このフィールドは指定された日付より後でなければなりません。
before
: このフィールドは指定された日付より前でなければなりません。
比較バリデーション
confirmed: このフィールドは field_confirmation と一致している必要があります（例: password と password_confirmation）。
same
: このフィールドは指定した他のフィールドと一致していなければなりません。
different
: このフィールドは指定した他のフィールドと異なっていなければなりません。
ファイルバリデーション
file: このフィールドはファイルでなければなりません。
image: このフィールドは画像ファイルでなければなりません。
mimes
: このフィールドは指定した MIME タイプのファイルでなければなりません（例: jpg,png）。
mimetypes
: このフィールドは指定した MIME タイプに一致するファイルでなければなりません。
max
: ファイルの最大サイズ（キロバイト）。
dimensions: 画像の最大／最小幅や高さなどを指定できます。
配列バリデーション
array: このフィールドは配列でなければなりません。
in
,bar,...: フィールドの値が指定したリストの中のいずれかである必要があります。
not_in
,bar,...: フィールドの値が指定したリストの中に含まれていてはいけません。
その他のバリデーション
unique
,column: このフィールドは指定されたテーブルのカラムにおいて一意でなければなりません。
exists
,column: このフィールドの値は指定されたテーブルのカラムに存在する必要があります。
regex
: このフィールドの値は指定された正規表現パターンに一致している必要があります。
カスタムバリデーション
custom rules: 自分で定義したカスタムバリデーションルールも使用できます。

*/

    public function getImages($item_id)
    {
        $images = ItemImage::where('item_id', $item_id)->get();
        return view('admin.items.partials.image-list', compact('images'));
    }

    // コース設定更新
    public function updateCourse(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:ok,ng,confirm',
        ]);

        $item = Item::findOrFail($id);
        $courseId = $request->input('course_id');
        $status = $request->input('status');

        // 既存の関連を削除して新しいステータスで追加
        $item->courses()->detach($courseId);

        if ($status !== 'ng') {
            $item->courses()->attach($courseId, ['status' => $status]);
        }

        return response()->json(['success' => true]);
    }

    // 料金設定更新
    public function updateCourseFee(Request $request, $id)
    {
        \Log::info('料金設定更新リクエスト:', $request->all());

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'fee_60' => 'nullable|integer|min:0',
            'fee_90' => 'nullable|integer|min:0',
            'fee_120' => 'nullable|integer|min:0',
            'fee_150' => 'nullable|integer|min:0',
            'fee_180' => 'nullable|integer|min:0',
            'fee_extension' => 'nullable|integer|min:0',
            'uniform_rate' => 'nullable|integer|min:0',
            'use_uniform_rate' => 'nullable|in:true,false,0,1',
        ]);

        $item = Item::findOrFail($id);
        $courseId = $request->input('course_id');

        $useUniformRate = $request->input('use_uniform_rate');
        $useUniformRate = ($useUniformRate === 'true' || $useUniformRate === true || $useUniformRate === '1' || $useUniformRate === 1) ? true : false;

        $feeData = [
            'fee_60' => $request->input('fee_60') ?: null,
            'fee_90' => $request->input('fee_90') ?: null,
            'fee_120' => $request->input('fee_120') ?: null,
            'fee_150' => $request->input('fee_150') ?: null,
            'fee_180' => $request->input('fee_180') ?: null,
            'fee_extension' => $request->input('fee_extension') ?: null,
            'uniform_rate' => $request->input('uniform_rate') ?: null,
            'use_uniform_rate' => $useUniformRate,
        ];

        \Log::info('料金設定データ:', $feeData);

        // 既存の料金設定を削除して新しい設定で追加
        $item->courseFees()->detach($courseId);
        $item->courseFees()->attach($courseId, $feeData);

        \Log::info('料金設定更新完了');

        return response()->json(['success' => true]);
    }

    /**
     * クーポン設定を更新
     */
    public function updateCoupons(Request $request, $id)
    {
        $request->validate([
            'coupons' => 'nullable|array',
            'coupons.*' => 'exists:coupons,id',
        ]);

        $item = Item::findOrFail($id);

        // 既存のクーポン関連付けを削除
        $item->coupons()->detach();

        // 新しいクーポン関連付けを追加
        if ($request->has('coupons')) {
            $coupons = $request->input('coupons', []);
            foreach ($coupons as $couponId) {
                $item->coupons()->attach($couponId, [
                    'coupon_data' => json_encode([]) // デフォルトの空データ
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    // =========================================================
    // インライン マイコスチューム管理
    // =========================================================

    public function storeMycostume(Request $request, Item $item)
    {
        $request->validate([
            'mycostume_cate_id' => 'required|exists:mycostume_cates,id',
            'name'              => 'required|string|max:255',
            'body'              => 'nullable|string',
            'price'             => 'nullable|integer|min:0',
            'active'            => 'nullable|boolean',
            'image'             => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $filename = 'mycostume_' . time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('mycostume', $filename);

        $costume = Mycostume::create([
            'item_id'          => $item->id,
            'mycostume_cate_id' => $request->mycostume_cate_id,
            'name'             => $request->name,
            'body'             => $request->body,
            'filename'         => $filename,
            'price'            => $request->price ?? 0,
            'active'           => $request->active ?? 1,
            'sort'             => Mycostume::where('item_id', $item->id)->max('sort') + 1,
        ]);
        $costume->load('category');

        return response()->json([
            'success' => true,
            'costume' => $costume,
            'image_url' => Storage::url('mycostume/' . $filename),
        ]);
    }

    public function updateMycostume(Request $request, Item $item, Mycostume $mycostume)
    {
        $request->validate([
            'mycostume_cate_id' => 'required|exists:mycostume_cates,id',
            'name'              => 'required|string|max:255',
            'body'              => 'nullable|string',
            'price'             => 'nullable|integer|min:0',
            'active'            => 'nullable|boolean',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $data = [
            'mycostume_cate_id' => $request->mycostume_cate_id,
            'name'              => $request->name,
            'body'              => $request->body,
            'price'             => $request->price ?? 0,
            'active'            => $request->active ?? $mycostume->active,
        ];

        if ($request->hasFile('image')) {
            if ($mycostume->filename && Storage::exists('mycostume/' . $mycostume->filename)) {
                Storage::delete('mycostume/' . $mycostume->filename);
            }
            $filename = 'mycostume_' . time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('mycostume', $filename);
            $data['filename'] = $filename;
        }

        $mycostume->update($data);
        $mycostume->load('category');

        return response()->json([
            'success'   => true,
            'costume'   => $mycostume,
            'image_url' => Storage::url('mycostume/' . $mycostume->filename),
        ]);
    }

    public function destroyMycostume(Request $request, Item $item, Mycostume $mycostume)
    {
        if ($mycostume->filename && Storage::exists('mycostume/' . $mycostume->filename)) {
            Storage::delete('mycostume/' . $mycostume->filename);
        }
        $mycostume->delete();
        return response()->json(['success' => true]);
    }

    public function toggleMycostumeActive(Request $request, Item $item, Mycostume $mycostume)
    {
        $mycostume->update(['active' => !$mycostume->active]);
        return response()->json(['success' => true, 'active' => (bool) $mycostume->active]);
    }

    // =========================================================
    // インライン マイオプション管理
    // =========================================================

    public function storeMyoption(Request $request, Item $item)
    {
        $request->validate([
            'myoption_cate_id' => 'required|exists:myoption_cates,id',
            'name'             => 'required|string|max:255',
            'body'             => 'nullable|string',
            'price'            => 'nullable|integer|min:0',
            'active'           => 'nullable|boolean',
            'image'            => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $filename = 'myoption_' . time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('myoption', $filename);

        $option = Myoption::create([
            'item_id'          => $item->id,
            'myoption_cate_id' => $request->myoption_cate_id,
            'name'             => $request->name,
            'body'             => $request->body,
            'filename'         => $filename,
            'price'            => $request->price ?? 0,
            'active'           => $request->active ?? 1,
            'sort'             => Myoption::where('item_id', $item->id)->max('sort') + 1,
        ]);
        $option->load('category');

        return response()->json([
            'success'   => true,
            'option'    => $option,
            'image_url' => Storage::url('myoption/' . $filename),
        ]);
    }

    public function updateMyoption(Request $request, Item $item, Myoption $myoption)
    {
        $request->validate([
            'myoption_cate_id' => 'required|exists:myoption_cates,id',
            'name'             => 'required|string|max:255',
            'body'             => 'nullable|string',
            'price'            => 'nullable|integer|min:0',
            'active'           => 'nullable|boolean',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $data = [
            'myoption_cate_id' => $request->myoption_cate_id,
            'name'             => $request->name,
            'body'             => $request->body,
            'price'            => $request->price ?? 0,
            'active'           => $request->active ?? $myoption->active,
        ];

        if ($request->hasFile('image')) {
            if ($myoption->filename && Storage::exists('myoption/' . $myoption->filename)) {
                Storage::delete('myoption/' . $myoption->filename);
            }
            $filename = 'myoption_' . time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('myoption', $filename);
            $data['filename'] = $filename;
        }

        $myoption->update($data);
        $myoption->load('category');

        return response()->json([
            'success'   => true,
            'option'    => $myoption,
            'image_url' => Storage::url('myoption/' . $myoption->filename),
        ]);
    }

    public function destroyMyoption(Request $request, Item $item, Myoption $myoption)
    {
        if ($myoption->filename && Storage::exists('myoption/' . $myoption->filename)) {
            Storage::delete('myoption/' . $myoption->filename);
        }
        $myoption->delete();
        return response()->json(['success' => true]);
    }

    public function toggleMyoptionActive(Request $request, Item $item, Myoption $myoption)
    {
        $myoption->update(['active' => !$myoption->active]);
        return response()->json(['success' => true, 'active' => (bool) $myoption->active]);
    }

    // 一括処理：指定日より以前のキャストを非公開または削除
    public function bulkAction(Request $request)
    {
        $request->validate([
            'before_date' => 'required|date',
            'action'      => 'required|in:hide,delete',
        ]);

        $date  = \Carbon\Carbon::parse($request->before_date)->endOfDay();
        $items = Item::where('updated_at', '<', $date)->get();
        $count = $items->count();

        if ($count === 0) {
            return back()->with('info', '該当するキャストはいませんでした。');
        }

        if ($request->action === 'hide') {
            Item::where('updated_at', '<', $date)->update(['item_active' => 0]);
            return back()->with('success', "{$count}件のキャストを非公開にしました。");
        }

        foreach ($items as $item) {
            $item_id = $item->id;

            $images = ItemImage::where('item_id', $item_id)->get();
            foreach ($images as $image) {
                if (Storage::exists($image->filename)) Storage::delete($image->filename);
                $image->delete();
            }

            \DB::table('item_option')->where('item_id', $item_id)->delete();
            \DB::table('item_option_pay')->where('item_id', $item_id)->delete();
            \DB::table('item_profile')->where('item_id', $item_id)->delete();
            \DB::table('item_icon_item')->where('item_id', $item_id)->delete();
            \DB::table('item_rank')->where('item_id', $item_id)->delete();
            \DB::table('item_interview')->where('item_id', $item_id)->delete();
            \DB::table('item_course')->where('item_id', $item_id)->delete();
            \DB::table('item_course_fees')->where('item_id', $item_id)->delete();
            \DB::table('schedules')->where('item_id', $item_id)->delete();
            \DB::table('newfaces')->where('item_id', $item_id)->delete();

            $mycostumes = \DB::table('mycostumes')->where('item_id', $item_id)->get();
            foreach ($mycostumes as $mc) {
                if (isset($mc->filename) && Storage::exists($mc->filename)) Storage::delete($mc->filename);
            }
            \DB::table('mycostumes')->where('item_id', $item_id)->delete();

            $myoptions = \DB::table('myoptions')->where('item_id', $item_id)->get();
            foreach ($myoptions as $mo) {
                if (isset($mo->filename) && Storage::exists($mo->filename)) Storage::delete($mo->filename);
            }
            \DB::table('myoptions')->where('item_id', $item_id)->delete();

            $item->delete();
        }

        return back()->with('success', "{$count}件のキャストを削除しました。");
    }

}
