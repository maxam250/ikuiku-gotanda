<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Member;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\ImageCompressor;

class GalleryController extends Controller
{
/**
     * Display a listing of the resource.
     */
    public function index()
    {
        $memberId = Auth::guard('member')->id();
        $items = Item::orderBy('item_name', 'asc')->get();
        $galleries = Gallery::where('member_id', $memberId)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('member.galleries.index', compact('galleries', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::orderBy('item_name', 'asc')->get();
        return view('member.galleries.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
            'active' =>  'in:0,1',
            'flag' =>  'in:0,1,2',
            'description' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);

        $memberId = Auth::guard('member')->id();

        // 新しいギャラリーを作成
        $gallery = new Gallery([
            'title' => $request->title,
            'item_id' => $request->item_id,
            'member_id' => $memberId,
            'active' => $request->active ?? 0,
            'flag' => $request->flag ?? 0,
            'description' => $request->description,
            'sort' => $request->sort ?? 0,
        ]);

        $gallery->save();

        return redirect()->route('member.gallery.edit', $gallery->id)
            ->with('success', 'ギャラリーが作成されました');
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
        $memberId = Auth::guard('member')->id();
        $items = Item::orderBy('item_name', 'asc')->get();

        $gallery = Gallery::with(['images' => function ($query) {
            $query->orderBy('sort', 'asc');
        }])->where('member_id', $memberId)
          ->findOrFail($id);

        return view('member.galleries.edit', compact('gallery', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
            'active' =>  'in:0,1',
            'flag' =>  'in:0,1,2',
            'description' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);

        $memberId = Auth::guard('member')->id();
        $gallery = Gallery::where('member_id', $memberId)->findOrFail($id);

        $gallery->update([
            'title' => $request->title,
            'item_id' => $request->item_id,
            'active' => $request->active ?? 0,
            'flag' => $request->flag ?? 0,
            'description' => $request->description,
            'sort' => $request->sort ?? 0,
        ]);

        return redirect()->route('member.gallery.edit', $gallery->id)
            ->with('success', 'ギャラリーが更新されました');
    }

    /**
     * 画像のアップロード
     */
    public function uploadImages(Request $request, $galleryId)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $memberId = Auth::guard('member')->id();
        $gallery = Gallery::where('member_id', $memberId)->findOrFail($galleryId);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $path =  $filename;
                $image->storeAs('gallery', $filename);
                ImageCompressor::compressIfNeeded(storage_path('app/gallery/' . $filename));

                // gallery_idを明示的に指定してGalleryImageを作成
                $galleryImage = new GalleryImage();
                $galleryImage->gallery_id = $gallery->id;
                $galleryImage->filename = $path;
                $galleryImage->sort = 0;
                $galleryImage->save();
            }
        }

        return redirect()->back()->with('success', '画像がアップロードされました');
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
     * 画像の削除
     */
    public function destroyImage($galleryId, $imageId)
    {
        $memberId = Auth::guard('member')->id();

        // ギャラリーの所有者確認
        $gallery = Gallery::where('member_id', $memberId)
            ->findOrFail($galleryId);

        // 画像の存在確認とギャラリーに属しているか確認
        $image = GalleryImage::where('gallery_id', $galleryId)
            ->where('id', $imageId)
            ->firstOrFail();

        $path = public_path($image->filename);

        if (file_exists($path)) {
            unlink($path);
        }

        $image->delete();

        return back()->with('success', '画像を削除しました');
    }

    /**
     * 画像の並び替え
     */
    public function sortImages(Request $request, $galleryId)
    {
        foreach ($request->order as $item) {
            GalleryImage::where('id', $item['id'])
                       ->where('gallery_id', $galleryId)
                       ->update([
                           'sort' => $item['position']
                       ]);
        }
        return response()->json(['status' => 'success', 'message' => '並び順を更新しました']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $memberId = Auth::guard('member')->id();
        $gallery = Gallery::where('member_id', $memberId)->findOrFail($id);

        // ギャラリー画像の削除
        foreach ($gallery->images as $image) {
            if (Storage::exists('gallery/' . $image->filename)) {
                Storage::delete('gallery/' . $image->filename);
            }
            $image->delete();
        }

        $gallery->delete();

        return redirect()->route('member.gallery.index')
            ->with('success', 'ギャラリーが削除されました');
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
