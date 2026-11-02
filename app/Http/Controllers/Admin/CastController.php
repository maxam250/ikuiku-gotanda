<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\CastImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;



class CastController extends Controller
{
    //
    public function index()
    {
        // $casts = Cast::all();
        $casts = Cast::with('thumbnail')->orderBy('cast_sort', 'asc')->get();
        return view('admin.casts.index', compact('casts'));
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
    public function show(string $cast_id)
    {
        //
    }


    public function store(Request $request)
    {

        // dd($request);
        // バリデーション
        $validated = $request->validate([
            'cast_name' => 'required|string|max:255',
            // 他の必要なフィールドも追加
        ]);

        // 新しいキャストを作成
        $cast = new Cast();
        $cast->cast_name = $validated['cast_name'];
        // 他のフィールドも追加

        $cast->save(); // データベースに保存

        // 成功したらリダイレクト
        return redirect()->route('admin.casts.edit', $cast->id)->with('success', 'キャストが追加されました');

    }



    public function edit(Cast $cast )
    {
        //echo $cast_id;
        $cast_id = $cast->id;

        // 画像リストを取得
        $images = CastImage::where('cast_id', $cast_id)->get();

        // dd( $castImages);

        return view('admin.casts.edit', compact('cast', 'images'));
    }

// NK　ここで更新するカラムのリストを設定する

    public function update(Request $request, $cast_id)
    {

       // $cast_id = $cast->id;



        //　バリデータ
        $request->validate([
            'cast_name' => 'required|string|max:255',
            'cast_mail' => 'nullable|string|email|max:255|unique:casts,cast_mail,' . $cast_id,
            'cast_password' => 'nullable|string|min:4',
        ]);

        // 項目の取得
        $cast = Cast::findOrFail($cast_id);


        // フォームからのデータをユーザーに一括で代入
        $cast->fill($request->except('cast_password', 'cast_active', 'cast_p_comment_date'));

        // パスワードの暗号化と更新
        // $cast->cast_mail = $request->input('cast_mail');

        if ($request->filled('cast_password')) {
            $cast->cast_password = Hash::make($request->input('cast_password'));
        }

        // checkboxをon→0/1に変換
        // $cast->cast_active = $request->input('cast_active') === 'on' ? 1:0;
        $cast->cast_active = $request->has('cast_active') ? 1 : 0;

        // リアルタイムコメント投稿時間
        $cast_p_comment_date = date('Y-m-d H:i:s');
        $cast->cast_p_comment_date = $cast_p_comment_date;

// echo $request->input('cast_password');
// echo $request->input('cast_active');
// echo "<br>";
// echo $cast->cast_active;
// echo "<br>";
// echo $cast->cast_name;


        $cast->save();

        // $cast->update($request->only(
        //     'cast_name',
        //     'cast_active',
        //     'cast_age',
        //     'cast_height',
        //     'cast_b',
        //     'cast_cup',
        //     'cast_w',
        //     'cast_h',
        //     'cast_shoes',
        //     'cast_comment',
        //     'cast_comment2',
        //     'cast_blogname',
        //     'cast_blog',
        //     'cast_twitter',
        //     'cast_url',
        //     'cast_line',
        //     'cast_instagram',
        //     'cast_youtube',
        //     'cast_tiktok',
        //     'cast_mail',
        //     'cast_tel',
        //     'cast_password',
        //     'cast_p_comment',
        //     'cast_p_comment_date',
        // ));


        // return redirect()->route('admin.casts.index')->with('success', 'アップデート成功');//そのままダッシュボードへ

        return redirect()->route('admin.casts.edit',['cast' => $cast_id])->with('success', 'アップデート成功');

        //var_dump($request);
    }


    //sort
    public function sort (Request $request)
    {
        $order = $request->input('order');

        foreach($order as $item){
            $cast = Cast::find($item['id']);

// dd($cast);


            $cast->cast_sort = $item['cast_sort'];
            $cast->save();
        }

        return response()->json([
            'success' => true,
            'message' => '並び替えしました'
        ]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $cast_id)
    {
        //
    }



    public function sortImage($cast_id)
    {
        // 必要なデータを取得
        // $cast = Cast::findOrFail($cast_id);
        $images = Castimg::where('cast_id', $cast_id)->get();

        // ビューにデータを渡して表示
        return view('admin.casts.sort', [
            // 'cast' => $cast,
            'images' => $images,
            'url' => route('admin.casts.sort', ['id' => $cast_id])
        ]);
    }




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


}
