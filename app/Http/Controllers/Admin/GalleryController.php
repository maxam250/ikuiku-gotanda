<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Shop;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageCompressor;
use Symfony\Component\Process\Process;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::orderBy('item_name', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();
        $galleries = Gallery::with(['images' => function ($query) {
            $query->orderBy('sort', 'asc');
        }])->orderBy('sort', 'asc')->orderBy('id', 'desc')->paginate(30);

        return view('admin.galleries.index', compact('galleries','items','members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $items = Item::orderBy('item_name', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        return view('admin.galleries.create', compact('items',  'members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 0の値をnullに変換（未選択の場合）
        if ($request->item_id == '0' || $request->item_id === 0) {
            $request->merge(['item_id' => null]);
        }
        if ($request->member_id == '0' || $request->member_id === 0) {
            $request->merge(['member_id' => null]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
            'member_id' => 'nullable|integer|exists:members,id',
            'active' =>  'in:0,1',
            'flag' =>  'in:0,1,2',
            'description' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);

        // 新しいギャラリーを作成
        $gallery = Gallery::create([
            'title' => $request->title,
            'item_id' => $request->item_id ?: null,
            'member_id' => $request->member_id ?: null,
            'active' => $request->active ?? 0,
            'flag' => $request->flag ?? 0,
            'description' => $request->description,
            'sort' => $request->sort ?? 0,
        ]);

        // 成功したらリダイレクト
        return redirect()->route('admin.gallery.edit', $gallery->id)->with('success', 'ギャラリーが追加されました');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $items = Item::orderBy('item_name', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();


        $gallery = Gallery::with(['images' => function ($query) {
            $query->orderBy('sort', 'asc');
        }])->findOrFail($id);


        return view('admin.galleries.edit', compact('gallery', 'items',  'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $gallery)
    {
        // 0の値をnullに変換（未選択の場合）
        if ($request->item_id == '0' || $request->item_id === 0) {
            $request->merge(['item_id' => null]);
        }
        if ($request->member_id == '0' || $request->member_id === 0) {
            $request->merge(['member_id' => null]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
            'member_id' => 'nullable|integer|exists:members,id',
            'active' =>  'in:0,1',
            'flag' =>  'in:0,1,2',
            'description' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);

        $gallery = Gallery::findOrFail($gallery);

        $gallery->update([
            'title' => $request->title,
            'item_id' => $request->item_id ?: null,
            'member_id' => $request->member_id ?: null,
            'active' => $request->active ?? 0,
            'flag' => $request->flag ?? 0,
            'description' => $request->description,
            'sort' => $request->sort ?? 0,
        ]);
        return redirect()->route('admin.gallery.edit', $gallery->id)->with('success', 'ギャラリーが更新されました');
    }

    /**
     * 画像のアップロード
     */
    public function uploadImages(Request $request, $galleryId)
    {
        \Log::info('Upload images method called', [
            'gallery_id' => $galleryId,
            'files' => $request->hasFile('images') ? count($request->file('images')) : 0
        ]);

        $request->validate([
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $gallery = Gallery::findOrFail($galleryId);

        foreach ($request->file('images') as $file) {
            \Log::info('Processing file', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            $filename = 'gallery_' . time() . '_' . $file->getClientOriginalName();

            try {
                $file->storeAs('gallery', $filename);
                ImageCompressor::compressIfNeeded(storage_path('app/gallery/' . $filename));
                \Log::info('File saved successfully', ['filename' => $filename]);

                $gallery->images()->create([
                    'filename' => $filename,
                    'sort' => 0,
                ]);
            } catch (\Exception $e) {
                \Log::error('File save failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return back()->with('success', '画像を追加しました');
    }


    /**
     * 画像の更新
     */
    public function updateImage(Request $request, $id)
    {
        $request->validate(['caption' => 'nullable|string|max:255']);
        $image = GalleryImage::findOrFail($id);
        $image->caption = $request->caption;
        $image->save();
        return back()->with('success', '画像情報を更新しました');
    }

    /**
     * 画像・動画の削除
     */
    public function deleteImage($galleryId, $imageId)
    {
        $gallery = Gallery::findOrFail($galleryId);
        $image = $gallery->images()->findOrFail($imageId);

        if ($image->type === 'video') {
            // 動画は public ディスクに保存
            if (Storage::disk('public')->exists($image->filename)) {
                Storage::disk('public')->delete($image->filename);
            }
        } else {
            // 画像はデフォルトディスクに保存
            if (Storage::exists('gallery/' . $image->filename)) {
                Storage::delete('gallery/' . $image->filename);
            }
        }

        $image->delete();

        return back()->with('success', '削除しました');
    }

    /**
     * 動画カードのアップロード（gallery_images に保存、最大3件・15MB・FFmpeg圧縮）
     */
    public function uploadVideoCard(Request $request, $galleryId)
    {
        try {
            $gallery = Gallery::findOrFail($galleryId);

            // 最大3件チェック
            $videoCount = $gallery->images()->where('type', 'video')->count();
            if ($videoCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => '動画は最大3件までアップロードできます',
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $validator = \Validator::make($request->all(), [
                'video' => 'required|file|mimes:mp4,mov,webm|max:' . (15 * 1024),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->messages()->first('video'),
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $file    = $request->file('video');
            $timeStr = time();
            $dir     = storage_path('app/public/gallery/video');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $tempFilename = 'temp_gvid_' . $galleryId . '_' . $timeStr . '.mp4';
            $tempPath     = $dir . DIRECTORY_SEPARATOR . $tempFilename;
            $outFilename  = 'gvid_' . $galleryId . '_' . $timeStr . '.mp4';
            $outPath      = $dir . DIRECTORY_SEPARATOR . $outFilename;
            $storagePath  = 'gallery/video/' . $outFilename;

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

            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            if (!$process->isSuccessful() || !file_exists($outPath)) {
                \Log::error('Gallery VideoCard FFmpeg error: ' . $process->getErrorOutput());
                return response()->json([
                    'success' => false,
                    'message' => '動画の圧縮に失敗しました。FFmpegのパス設定を確認してください。',
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $imageRecord = $gallery->images()->create([
                'filename' => $storagePath,
                'type'     => 'video',
                'caption'  => null,
                'sort'     => 0,
            ]);

            return response()->json([
                'success'    => true,
                'image_id'   => $imageRecord->id,
                'video_url'  => Storage::disk('public')->url($storagePath),
                'remaining'  => 2 - $videoCount,
                'delete_url' => route('admin.gallery.image.delete', [
                    'galleryId' => $galleryId,
                    'imageId'   => $imageRecord->id,
                ]),
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Gallery VideoCard upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '動画のアップロード中にエラーが発生しました: ' . $e->getMessage(),
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 画像の並び替え
     */
    public function sortImages(Request $request, $galleryId)
    {
        foreach ($request->order as $item) {
            GalleryImage::where('id', $item['id'])->where('gallery_id', $galleryId)->update([
                'sort' => $item['sort']
            ]);
        }
        return response()->json(['status' => 'ok']);
    }


    /**
     * ギャラリーを削除
     */
    public function destroy($gallery)
    {
        $gallery = Gallery::findOrFail($gallery);

        // 画像が存在している場合は削除
        foreach ($gallery->images as $image) {
            if (Storage::exists('gallery/' . $image->filename)) {
                Storage::delete('gallery/' . $image->filename);
            }
        }

        // ギャラリーデータの削除
        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'ギャラリーが削除されました');
    }



    /**
     * 動画のアップロード（FFmpegで圧縮）
     */
    public function uploadVideo(Request $request, $galleryId)
    {
        try {
            $maxMb = config('media.video.max_mb', 50);

            $validator = \Validator::make($request->all(), [
                'video' => 'required|file|mimes:mp4,mov,webm|max:' . ($maxMb * 1024),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->messages()->first('video'),
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $gallery = Gallery::findOrFail($galleryId);

            // 既存動画を削除
            if ($gallery->gallery_video && Storage::disk('public')->exists($gallery->gallery_video)) {
                Storage::disk('public')->delete($gallery->gallery_video);
            }

            $file    = $request->file('video');
            $timeStr = time();
            $dir     = storage_path('app/public/gallery/video');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $tempFilename = 'temp_gallery_' . $galleryId . '_' . $timeStr . '.mp4';
            $tempPath     = $dir . DIRECTORY_SEPARATOR . $tempFilename;
            $outFilename  = 'gallery_video_' . $galleryId . '_' . $timeStr . '.mp4';
            $outPath      = $dir . DIRECTORY_SEPARATOR . $outFilename;
            $storagePath  = 'gallery/video/' . $outFilename;

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
                \Log::error('Gallery FFmpeg error: ' . $process->getErrorOutput());
                return response()->json([
                    'success' => false,
                    'message' => '動画の圧縮に失敗しました。FFmpegのパス設定を確認してください。',
                ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            $gallery->gallery_video = $storagePath;
            $gallery->save();

            return response()->json([
                'success'   => true,
                'video_url' => Storage::disk('public')->url($storagePath),
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Gallery video upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '動画のアップロード中にエラーが発生しました: ' . $e->getMessage(),
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 動画の削除
     */
    public function deleteVideo($galleryId)
    {
        try {
            $gallery = Gallery::findOrFail($galleryId);

            if ($gallery->gallery_video && Storage::disk('public')->exists($gallery->gallery_video)) {
                Storage::disk('public')->delete($gallery->gallery_video);
            }

            $gallery->gallery_video = null;
            $gallery->save();

            return response()->json([
                'success' => true,
                'message' => '動画を削除しました',
            ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Gallery video delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '動画の削除中にエラーが発生しました: ' . $e->getMessage(),
            ], 500, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // イベントを並び替え順序に基づいて更新
            // gallery::where('id', $item['id'])->update(['sort' => $item['position']]);

            // timestampsの自動更新を無効化して、並び替えを保存
            $gallery = gallery::find($item['id']);
            $gallery->timestamps = false;  // updated_atとcreated_atを無効化
            $gallery->sort = $item['position'];
            $gallery->save();
        }

        return response()->json(['success' => true]);
    }

}
