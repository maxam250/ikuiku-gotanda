<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;


// ImageManagerクラスの存在確認（デバッグ用、本番では削除推奨）
// if (class_exists('Intervention\Image\ImageManager')) {
//     \Log::info('ImageManager class exists');
// } else {
//     \Log::error('ImageManager class does not exist');
// }

class ItemImageController extends Controller
{

    /**uploadImage
     *　画像のアップロード
     */

    //Ajax
    public function uploadImage(Request $request, $item_id)
    {

        try {
            // バリデーションルールを定義
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3024',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'エラーが発生しました①: ' . $validator->messages()->first('image'),
                    'error_code' => 1
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            // 画像の取得
            $image = $request->file('image');
            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'エラーが発生しました②: 画像ファイルが見つかりません',
                    'error_code' => 2
                ], 400, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $imageName = 'item_'.$item_id."_".time() . '.' . $image->getClientOriginalExtension();

            // storage/app/public/item に保存（public/を除く）
            $path = $image->storeAs('item', $imageName, 'public');
            if (!$path) {
                \Log::error('Failed to store image: ' . $imageName);
                return response()->json([
                    'success' => false,
                    'message' => 'エラーが発生しました③: 画像の保存に失敗しました',
                    'error_code' => 3
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            // 保存されたファイルの存在確認
            $fullPath = storage_path('app/public/' . $path);
            if (!file_exists($fullPath)) {
                \Log::error('Image file not found after storage: ' . $fullPath);
                return response()->json([
                    'success' => false,
                    'message' => 'エラーが発生しました④: 画像ファイルの保存に失敗しました',
                    'error_code' => 4
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            try {
                // Intervention Image v3系の推奨APIを使用
                $manager = new ImageManager(new Driver());
                $img = $manager->read($fullPath);

                $item_width = config('images.item_width');
                $item_height = config('images.item_height');

                $img = $img->scaleDown($item_width, $item_height); // 切り抜かずに縮小のみ（トリミングは CSS 側）
                $img->toJpeg(80)->save($fullPath);

            } catch (\Exception $e) {
                \Log::error('Image processing error: ' . $e->getMessage());
                // 画像処理に失敗しても、アップロード自体は成功とみなす
            }

            $imageSort = time();

            $itemImage = ItemImage::create([
                'item_id' => $item_id,
                'filename' => $path,
                'flag' => 1,
                'sort' => $imageSort,
            ]);

            if (!$itemImage) {
                // 画像ファイルを削除
                Storage::disk('public')->delete($path);
                \Log::error('Failed to save image record to database: ' . $imageName);
                return response()->json([
                    'success' => false,
                    'message' => 'エラーが発生しました⑤: データベースへの保存に失敗しました',
                    'error_code' => 5
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            \Log::info('Image uploaded successfully: ' . $imageName);
            return response()->json([
                'success' => true,
                'message' => '画像が正常にアップロードされました',
                'image_id' => $itemImage->id,
                'image_url' => Storage::disk('public')->url($path)
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました⑥: 画像のアップロード中にエラーが発生しました - ' . $e->getMessage(),
                'error_code' => 6
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

            // バリデーション
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3024',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'エラーが発生しました⑦: ' . $validator->messages()->first('image'),
                    'error_code' => 7
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            // 古い画像ファイルを削除
            if (Storage::disk('public')->exists($image->filename)) {
                Storage::disk('public')->delete($image->filename);
            }

             // 新しい画像を保存
             $newImage = $request->file('image');
             $newImageName = 'item_' . $image->item_id . '_' . time() . '.' . $newImage->getClientOriginalExtension();
             $path = $newImage->storeAs('item', $newImageName, 'public');

             // 画像のリサイズ処理
             $fullPath = storage_path('app/public/' . $path);
             $manager = new ImageManager(new Driver());
             $img = $manager->read($fullPath);
             $item_width = config('images.item_width');
             $item_height = config('images.item_height');
             $img = $img->scaleDown($item_width, $item_height); // 切り抜かずに縮小のみ（トリミングは CSS 側）
             $img->toJpeg(80)->save($fullPath);

             // データベースを更新
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
                 'message' => 'エラーが発生しました⑦: 画像の差し替え中にエラーが発生しました - ' . $e->getMessage(),
                 'error_code' => 7
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
                'message' => 'エラーが発生しました⑧: 画像の削除中にエラーが発生しました - ' . $e->getMessage(),
                'error_code' => 8
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * 動画のアップロード（FFmpegで圧縮）
     */
    public function uploadVideo(Request $request, $item_id)
    {
        try {
            $maxMb = config('media.video.max_mb', 50);

            $validator = Validator::make($request->all(), [
                'video' => 'required|file|mimes:mp4,mov,avi,webm|max:' . ($maxMb * 1024),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->messages()->first('video'),
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $item = Item::findOrFail($item_id);

            // 既存動画を削除
            if ($item->item_video && Storage::disk('public')->exists($item->item_video)) {
                Storage::disk('public')->delete($item->item_video);
            }

            $file    = $request->file('video');
            $timeStr = time();
            $dir     = storage_path('app/public/item/video');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $tempFilename = 'temp_item_' . $item_id . '_' . $timeStr . '.mp4';
            $tempPath     = $dir . DIRECTORY_SEPARATOR . $tempFilename;
            $outFilename  = 'item_video_' . $item_id . '_' . $timeStr . '.mp4';
            $outPath      = $dir . DIRECTORY_SEPARATOR . $outFilename;
            $storagePath  = 'item/video/' . $outFilename;

            // 一時ファイルとして保存
            $file->move($dir, $tempFilename);

            // FFmpeg 圧縮
            $ffmpegPath = config('media.ffmpeg_path', 'ffmpeg');
            $crf        = config('media.video.crf', 28);
            $preset     = config('media.video.preset', 'medium');
            $scale      = config('media.video.scale', 1280);

            $process = new Process([
                $ffmpegPath,
                '-i',        $tempPath,
                '-vcodec',   'libx264',
                '-crf',      (string)$crf,
                '-preset',   $preset,
                '-vf',       'scale=' . $scale . ':-2',
                '-acodec',   'aac',
                '-b:a',      '128k',
                '-movflags', '+faststart',
                '-y',
                $outPath,
            ]);
            $process->setTimeout(300);
            $process->run();

            // 一時ファイルを削除
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            if (!$process->isSuccessful() || !file_exists($outPath)) {
                \Log::error('Item FFmpeg error: ' . $process->getErrorOutput());
                return response()->json([
                    'success' => false,
                    'message' => '動画の圧縮に失敗しました。FFmpegのパス設定を確認してください。',
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $item->item_video = $storagePath;
            $item->save();

            return response()->json([
                'success'   => true,
                'video_url' => Storage::disk('public')->url($storagePath),
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Video upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '動画のアップロード中にエラーが発生しました: ' . $e->getMessage(),
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 動画の削除
     */
    public function deleteVideo($item_id)
    {
        try {
            $item = Item::findOrFail($item_id);

            if ($item->item_video && Storage::disk('public')->exists($item->item_video)) {
                Storage::disk('public')->delete($item->item_video);
            }

            $item->item_video = null;
            $item->save();

            return response()->json([
                'success' => true,
                'message' => '動画を削除しました',
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Video delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '動画の削除中にエラーが発生しました: ' . $e->getMessage(),
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * サムネイル動画のアップロード（FFmpegで圧縮）
     */
    public function uploadThumbnailVideo(Request $request, $item_id)
    {
        try {
            $maxMb = config('media.thumb_video.max_mb', 10);

            $validator = Validator::make($request->all(), [
                'video' => 'required|file|mimes:mp4|max:' . ($maxMb * 1024),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->messages()->first('video'),
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $item = Item::findOrFail($item_id);

            // 既存のサムネイル動画を削除
            if ($item->thumbnail_video && Storage::disk('public')->exists($item->thumbnail_video)) {
                Storage::disk('public')->delete($item->thumbnail_video);
            }

            $file    = $request->file('video');
            $timeStr = time();
            $dir     = storage_path('app/public/item/thumb_video');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $tempFilename  = 'temp_' . $item_id . '_' . $timeStr . '.mp4';
            $tempPath      = $dir . DIRECTORY_SEPARATOR . $tempFilename;
            $outFilename   = 'item_thumbvid_' . $item_id . '_' . $timeStr . '.mp4';
            $outPath       = $dir . DIRECTORY_SEPARATOR . $outFilename;
            $storagePath   = 'item/thumb_video/' . $outFilename;

            // 一時ファイルとして保存
            $file->move($dir, $tempFilename);

            // FFmpeg 圧縮
            $ffmpegPath = config('media.ffmpeg_path', 'ffmpeg');
            $crf        = config('media.thumb_video.crf', 30);
            $preset     = config('media.thumb_video.preset', 'medium');
            $scale      = config('media.thumb_video.scale', 480);

            $process = new Process([
                $ffmpegPath,
                '-i',         $tempPath,
                '-vcodec',    'libx264',
                '-crf',       (string)$crf,
                '-preset',    $preset,
                '-vf',        'scale=' . $scale . ':-2',
                '-an',
                '-movflags',  '+faststart',
                '-y',
                $outPath,
            ]);
            $process->setTimeout(120);
            $process->run();

            // 一時ファイルを削除
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            if (!$process->isSuccessful() || !file_exists($outPath)) {
                \Log::error('FFmpeg error: ' . $process->getErrorOutput());
                return response()->json([
                    'success' => false,
                    'message' => '動画の圧縮に失敗しました。FFmpegのパス設定を確認してください。',
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $item->thumbnail_video = $storagePath;
            $item->save();

            return response()->json([
                'success'   => true,
                'video_url' => Storage::disk('public')->url($storagePath),
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Thumbnail video upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'サムネイル動画のアップロード中にエラーが発生しました: ' . $e->getMessage(),
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * サムネイル動画の削除
     */
    public function deleteThumbnailVideo($item_id)
    {
        try {
            $item = Item::findOrFail($item_id);

            if ($item->thumbnail_video && Storage::disk('public')->exists($item->thumbnail_video)) {
                Storage::disk('public')->delete($item->thumbnail_video);
            }

            $item->thumbnail_video = null;
            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'サムネイル動画を削除しました',
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Thumbnail video delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'サムネイル動画の削除中にエラーが発生しました: ' . $e->getMessage(),
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    //sort
    public function sortImage(Request $request, $item_id)
    {

        $order = $request->input('order');


        foreach ($order as $item) {
            ItemImage::where('id', $item['id'])
                     ->where('item_id', $item_id)
                     ->update(['sort' => $item['item_sort']]);
        }

        return response()->json(['success' => true], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }



    /**
     *　サムネイルの設定
     */


    public function setThumbnail(Request $request, $item_id)
    {
        $imageId = $request->input('image_id');
        // 以前のサムネイル設定をリセット
        ItemImage::where('item_id', $item_id)->update(['flag' => 0]);
        // 新しいサムネイルを設定
        $image = ItemImage::where('id', $imageId)->first();
        if ($image) {
            $image->flag = 1;
            $image->save();
            return response()->json(['success' => true], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
        return response()->json(['success' => false], 400, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }


}
