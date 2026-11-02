<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;



class ItemImageController extends Controller
{

    /**uploadImage
     *　画像のアップロード
     */

    //Ajax
    public function uploadImage(Request $request, $item_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3024',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->messages()->first('image')
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $image = $request->file('image');
            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => '画像ファイルが見つかりません'
                ], 400, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $imageName = 'item_'.$item_id."_".time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('item', $imageName, 'public');

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => '画像の保存に失敗しました'
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $fullPath = storage_path('app/public/' . $path);
            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => '画像ファイルの保存に失敗しました'
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            try {
                $manager = new ImageManager(new Driver());
                $img = $manager->read($fullPath);
                $item_width = config('images.item_width');
                $item_height = config('images.item_height');
                $img->scaleDown($item_width, $item_height); // 切り抜かずに縮小のみ（トリミングは CSS 側）
                $img->toJpeg()->save($fullPath);
            } catch (\Exception $e) {
                \Log::error('Image processing error: ' . $e->getMessage());
            }

            $imageSort = time();
            $itemImage = ItemImage::create([
                'item_id' => $item_id,
                'filename' => $path,
                'flag' => 1,
                'sort' => $imageSort,
            ]);

            if (!$itemImage) {
                Storage::disk('public')->delete($path);
                return response()->json([
                    'success' => false,
                    'message' => 'データベースへの保存に失敗しました'
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            return response()->json([
                'success' => true,
                'image_url' => Storage::disk('public')->url($path),
                'image_id' => $itemImage->id
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '画像のアップロード中にエラーが発生しました: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     *　画像の更新
     */

     public function replaceImage(Request $request, $imageId)
     {
         try {
             $image = ItemImage::findOrFail($imageId);

             $validator = Validator::make($request->all(), [
                 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3024',
             ]);

             if ($validator->fails()) {
                 return response()->json([
                     'success' => false,
                     'message' => $validator->messages()->first('image')
                 ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
             }

             if (Storage::disk('public')->exists($image->filename)) {
                 Storage::disk('public')->delete($image->filename);
             }

             $newImage = $request->file('image');
             $newImageName = 'item_' . $image->item_id . '_' . time() . '.' . $newImage->getClientOriginalExtension();
             $path = $newImage->storeAs('item', $newImageName, 'public');

             $fullPath = storage_path('app/public/' . $path);
             try {
                 $manager = new ImageManager(new Driver());
                 $img = $manager->read($fullPath);
                 $item_width = config('images.item_width');
                 $item_height = config('images.item_height');
                 $img->scaleDown($item_width, $item_height); // 切り抜かずに縮小のみ（トリミングは CSS 側）
                 $img->toJpeg()->save($fullPath);
             } catch (\Exception $e) {
                 \Log::error('Image processing error: ' . $e->getMessage());
             }

             $image->filename = $path;
             $image->save();

             return response()->json([
                 'success' => true,
                 'newImageUrl' => Storage::disk('public')->url($path)
             ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
         } catch (\Exception $e) {
             \Log::error('Image replace error: ' . $e->getMessage());
             return response()->json([
                 'success' => false,
                 'message' => '画像の差し替え中にエラーが発生しました: ' . $e->getMessage()
             ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
         }
     }


    /**
     *　画像の削除
     */

    public function deleteImage($imageId)
    {
        try {
            $image = ItemImage::findOrFail($imageId);

            if(Storage::disk('public')->exists($image->filename)){
                Storage::disk('public')->delete($image->filename);
            }

            $image->delete();

            return response()->json([
                'success' => true,
                'message' => '画像を削除しました'
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Image delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '画像の削除中にエラーが発生しました: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }


    //sort
    public function sortImage(Request $request, $item_id)
    {
        try {
            $order = $request->input('order');

            foreach ($order as $item) {
                ItemImage::where('id', $item['id'])
                         ->where('item_id', $item_id)
                         ->update(['sort' => $item['item_sort']]);
            }

            return response()->json(['success' => true], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Image sort error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '画像の並び替え中にエラーが発生しました: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }



    /**
     *　サムネイルの設定
     */


    public function setThumbnail(Request $request, $item_id)
    {
        try {
            $imageId = $request->input('image_id');
            ItemImage::where('item_id', $item_id)->update(['flag' => 0]);
            $image = ItemImage::where('id', $imageId)->first();
            if ($image) {
                $image->flag = 1;
                $image->save();
                return response()->json(['success' => true], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
            return response()->json([
                'success' => false,
                'message' => '画像が見つかりません'
            ], 404, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Set thumbnail error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'サムネイル設定中にエラーが発生しました: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }


}
