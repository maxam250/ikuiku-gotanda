<?php

namespace App\Http\Controllers\Item;

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
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $items = Item::orderBy('item_name', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();
        $gallaries = Gallery::where('item_id', $item->id)
            ->with(['images' => function ($query) {
                $query->orderBy('sort', 'asc');
            }])->orderBy('sort', 'asc')->orderBy('id', 'desc')->paginate(30);

        return view('item.galleries.index', compact('gallaries','items','members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $items = Item::orderBy('item_name', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        return view('item.galleries.create', compact('items',  'members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
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

        $validated = $request->all();
        $validated['item_id'] = $item->id;

        // 新しいギャラリーを作成
        $gallery = new Gallery();
        $gallery->fill($validated);
        $gallery->save();

        return redirect()->route('item.gallery.edit', $gallery->id)->with('success', 'ギャラリーが追加されました');
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
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $items = Item::orderBy('item_name', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        $gallery = Gallery::where('item_id', $item->id)
            ->with(['images' => function ($query) {
                $query->orderBy('sort', 'asc');
            }])->findOrFail($id);

        return view('item.galleries.edit', compact('gallery', 'items',  'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $gallery)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
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

        $gallery = Gallery::where('item_id', $item->id)->findOrFail($gallery);

        $gallery->update([
            'title' => $request->title,
            'item_id' => $item->id,
            'member_id' => $request->member_id,
            'active' => $request->active ?? 0,
            'flag' => $request->flag ?? 0,
            'description' => $request->description,
            'sort' => $request->sort ?? 0,
        ]);
        return redirect()->route('item.gallery.edit', $gallery->id)->with('success', 'ギャラリーが更新されました');
    }

    /**
     * 画像のアップロード
     */
    public function uploadImages(Request $request, $galleryId)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $gallery = Gallery::where('item_id', $item->id)->findOrFail($galleryId);

        foreach ($request->file('images') as $file) {
            $filename = 'gallery_' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('gallery', $filename);

            $gallery->images()->create([
                'filename' => $filename,
                'sort' => 0,
            ]);
        }

        return back()->with('success', '画像を追加しました');
    }

    /**
     * 画像の更新
     */
    public function updateImage(Request $request, $id)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate(['caption' => 'nullable|string|max:255']);
        $image = GalleryImage::findOrFail($id);
        
        // ギャラリーがこのItemに属しているか確認
        $gallery = Gallery::where('item_id', $item->id)->findOrFail($image->gallery_id);
        
        $image->caption = $request->caption;
        $image->save();
        return back()->with('success', '画像情報を更新しました');
    }

    /**
     * 画像の削除
     */
    public function deleteImage($id)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $image = GalleryImage::findOrFail($id);
        
        // ギャラリーがこのItemに属しているか確認
        $gallery = Gallery::where('item_id', $item->id)->findOrFail($image->gallery_id);

        // 画像ファイルの削除
        if (Storage::exists('gallery/' . $image->filename)) {
            Storage::delete('gallery/' . $image->filename);
        }

        $image->delete();

        return back()->with('success', '画像を削除しました');
    }

    /**
     * 画像の並び替え
     */
    public function sortImages(Request $request, $galleryId)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $gallery = Gallery::where('item_id', $item->id)->findOrFail($galleryId);

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
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return redirect()->route('item.login');
        }

        $gallery = Gallery::where('item_id', $item->id)->findOrFail($gallery);

        // 画像が存在している場合は削除
        foreach ($gallery->images as $image) {
            if (Storage::exists('gallery/' . $image->filename)) {
                Storage::delete('gallery/' . $image->filename);
            }
        }

        // ギャラリーデータの削除
        $gallery->delete();

        return redirect()->route('item.gallery.index')->with('success', 'ギャラリーが削除されました');
    }

    //並び替え
    public function sort(Request $request)
    {
        $item = Auth::guard('item')->user();
        
        if (!$item) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $order = $request->input('order');

        foreach ($order as $orderItem) {
            $gallery = Gallery::where('item_id', $item->id)->find($orderItem['id']);
            if ($gallery) {
                $gallery->timestamps = false;
                $gallery->sort = $orderItem['position'];
                $gallery->save();
            }
        }

        return response()->json(['success' => true]);
    }
}

