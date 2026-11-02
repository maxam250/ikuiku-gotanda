<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\CastImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class CastImageController extends Controller
{

    /**
     *　画像のアップロード
     */

    //Ajax
    public function uploadImage(Request $request, $cast_id)
    {

        // バリデーションルールを定義
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3024', // 800KBの制限
        ]);

        // バリデーションエラーがある場合は、エラーメッセージと共にレスポンスを返す
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->first('image') // 最初のエラーメッセージを取得して返す
            ], 422); // 422 Unprocessable Entity ステータスコード
        }

        // 画像の取得
        $image = $request->file('image');
        $imageName = 'cast_'.$cast_id."_".time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads'), $imageName);



       // 希望するドライバーで新しいマネージャー インスタンスを作成する
       $manager = new ImageManager(new Driver());

       $imgPath = public_path('uploads/'.$imageName);
       $img = $manager->read($imgPath);


       $cast_width = config('images.cast_width');
       $cast_height = config('images.cast_height');


//intervention imageメソッド集
//https://blog.capilano-fw.com/?p=1574
//https://image.intervention.io/v3

        //縮小
        //    dd($img );
        //    $img->flop();

        //$img->scale(width: 400);    //リサイズ
        $img->cover($cast_width, $cast_height);      //リサイズ＆クロップ
        $img->save(public_path('uploads/' . $imageName));


        $imageSort = time();


        $castImage = CastImage::create([
            'cast_id' => $cast_id,
            'filename' => $imageName,
            'flag' => 1,
            'sort' => $imageSort,
        ]);

        // データベースへの保存が成功した場合のレスポンス
        if ($castImage) {
            return response()->json([
                'success' => true,
                'image_url' => asset('uploads/' . $imageName),
                'image_id' => $castImage->id
            ]);
        }

        // 保存に失敗した場合のエラーレスポンス
        return response()->json([
            'success' => false,
            'message' => 'Failed to save image'
        ], 500); // 500 Internal Server Error ステータスコード
    }

    /**
     *　画像の更新
     */

     public function replaceImage(Request $request, $imageId)
     {
         $image = CastImage::findOrFail($imageId);


         // 古い画像ファイルを削除
         $oldPath = public_path('uploads/' . $image->filename);
         if (File::exists($oldPath)) {
             File::delete($oldPath);
         }

         // 新しい画像を保存
         $newImage = $request->file('image');
         $newImageName = 'cast_' . $image->cast_id . '_' . time() . '.' . $newImage->getClientOriginalExtension();
         $newImage->move(public_path('uploads'), $newImageName);

         // データベースを更新
         $image->filename = $newImageName;
         $image->save();

         return response()->json([
             'success' => true,
             'newImageUrl' => asset('uploads/' . $newImageName)
         ]);
     }


    /**
     *　画像の削除
     */

    public function deleteImage($imageId)
    {

        $image = CastImage::findOrFail($imageId);
        $path = public_path('uploads/' . $image->filename);

        if(File::exists($path)){
            File::delete($path); //ファイル削除
        }

        $image->delete(); // データベースのレコードも削除


        return response()->json([
            'success' => 'true',
            'message' => '画像を削除しました'
        ]);
    }

    /**
     *　サムネイルの設定
     */


    public function setThumbnail(Request $request, $cast_id)
    {
        $imageId = $request->input('image_id');
        // 以前のサムネイル設定をリセット
        CastImage::where('cast_id', $cast_id)->update(['flag' => 0]);
        // 新しいサムネイルを設定
        $image = CastImage::where('id', $imageId)->first();
        if ($image) {
            $image->flag = 1;
            $image->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }


}
