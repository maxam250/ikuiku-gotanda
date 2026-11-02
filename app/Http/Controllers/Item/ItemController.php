<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\Profile;
use App\Models\IconItem;
use App\Models\Interview;
use App\Models\Course;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

// dd(\Route::currentRouteAction());

class ItemController extends Controller
{
    //

    public function index()
    {
        // $items = Item::all();
        $items = Item::with('thumbnail')->orderBy('item_sort', 'asc')->get();
        return view('item.items.index', compact('items'));
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

        // 新しいキャストを作成
        $item = new Item();
        $item->item_name = $validated['item_name'];
        // 他のフィールドも追加

        $item->save(); // データベースに保存

        // 成功したらリダイレクト
        return redirect()->route('item.items.edit', $item->id)->with('success', 'キャストが追加されました');

    }


    public function edit()
    {

        $item = Auth::guard('item')->user(); // ログイン中のキャスト情報を取得


        // 画像リストを取得
        $images = ItemImage::where('item_id', $item->id)->orderby('sort', 'asc')->get();

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

// dd($option_pays);


        // dd( $selectedOptions);

        return view('item.items.edit', compact('item', 'images','options', 'selectedOptions','option_pays', 'selectedOptionPays','profiles', 'profileContents','icon_items', 'selectedIconItems','interviews', 'interviewContents','courses', 'courseStatuses', 'courseFeeSettings'));
    }


    public function account()
    {

        $item = Auth::guard('item')->user(); // ログイン中のキャスト情報を取得



// dd($option_pays);


        // dd( $selectedOptions);

        return view('item.items.account', compact('item'));
    }

    // 更新するカラムのリストを設定

    public function update(Request $request, $id)
    {

        //　バリデータ
        $request->validate([
            'item_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:items,email,' . $id,
            'password' => 'nullable|string|min:4',
        ]);

        // 項目の取得
        $item = Item::findOrFail($id);


        // フォームからのデータをユーザーに一括で代入
        $item->fill($request->except('password', 'item_active', 'item_p_comment_date'));

        // パスワードの暗号化と更新
        // $item->email = $request->input('email');

        if ($request->filled('password')) {
            $item->password = Hash::make($request->input('password'));
        }

        // 公開チェック（マイページのフォームには item_active が無いため、その場合は既存値を維持）
        if ($request->has('item_active')) {
            $item->item_active = $request->boolean('item_active') ? 1 : 0;
        }

        // リアルタイムコメント投稿時間
        $item_p_comment_date = date('Y-m-d H:i:s');
        $item->item_p_comment_date = $item_p_comment_date;



        // オプションの関連付け更新　→Ajaxにしているため除外
        //$item->options()->sync($request->input('options', []));
        // オプションPAYの関連付け更新　→Ajaxにしているため除外
        //$item->option_pays()->sync($request->input('option_pays', []));
        $item->save();


        return redirect()->route('item.items.edit',['item' => $id])->with('success', 'アップデート成功');

        //var_dump($request);
    }


    public function accountUpdate(Request $request, $id)
    {

        //　バリデータ
        $request->validate([
            // 'item_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:items,email,' . $id,
            'password' => 'nullable|string|min:4',
        ]);

        // 項目の取得
        $item = Item::findOrFail($id);


        // フォームからのデータをユーザーに一括で代入
        $item->fill($request->except('password', 'item_active', 'item_p_comment_date'));

        // パスワードの暗号化と更新
        // $item->email = $request->input('email');

        if ($request->filled('password')) {
            $item->password = Hash::make($request->input('password'));
        }

        $item->save();


        return redirect()->route('item.items.account',['item' => $id])->with('success', 'アップデート成功');

        //var_dump($request);
    }


    //sort
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // キャストを並び替え順序に基づいて更新
            Item::where('id', $item['id'])->update(['item_sort' => $item['item_sort']]);
        }

        return response()->json(['success' => true]);
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $item_id)
    {
        //
    }



    public function sortImage($item_id)
    {
        // 必要なデータを取得
        // $item = Item::findOrFail($item_id);
        $images = ItemImage::where('item_id', $item_id)->get();

        // ビューにデータを渡して表示
        return view('item.items.sort', [
            // 'item' => $item,
            'images' => $images,
            'url' => route('item.items.sort', ['id' => $item_id])
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

// デバッグ: リクエストデータを確認
// dd($request->all());

        // バリデーション
        $request->validate([
            'profiles' => 'nullable|required|array',
            'profiles.*' => 'nullable|string|max:255',
            'profiles_id.*' => 'exists:profiles,id' // バリデーションで存在を確認
        ]);

        // 該当のアイテムを取得
        $item = Item::findOrFail($id);

        // 送信されたプロフィールデータを更新または追加
        $profilesData = $request->input('profiles');

        foreach ($profilesData as $profileId => $content) {
            // 中間テーブルにデータを挿入または更新
            $item->profiles()->syncWithoutDetaching([
                $profileId => ['content' => $content]
            ]);
        }

        return redirect()->route('item.items.edit', $item->id)->with('success', 'プロフィールが更新されました。');
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
        return view('item.items.partials.image-list', compact('images'));
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

        return redirect()->route('item.items.edit')->with('success', 'Q&Aが更新されました');
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
    public function updateCoupons(Request $request)
    {
        $request->validate([
            'coupons' => 'nullable|array',
            'coupons.*' => 'exists:coupons,id',
        ]);

        $item = Auth::guard('item')->user();
        
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

    /**
     * クーポン設定ページを表示
     */
    public function coupons()
    {
        $item = Auth::guard('item')->user(); // ログイン中のキャスト情報を取得

        // クーポンリストを取得
        $coupons = Coupon::active()->valid()->orderBy('title', 'asc')->get();
        // 選択されたクーポンをビューに渡す
        $selectedCoupons = $item->coupons->pluck('id')->toArray();

        return view('item.items.coupons', compact('item', 'coupons', 'selectedCoupons'));
    }

}
