<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mycostume;
use App\Models\MycostumeCate;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MycostumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // キャストごとにマイコスチュームを取得
        $items = Item::with(['mycostumes' => function ($query) {
            $query->orderBy('sort', 'asc');
        }])->whereHas('mycostumes')->orderBy('item_name', 'asc')->get();

        // カテゴリ別のカウントを計算
        $categoryCounts = [];
        foreach ($items as $item) {
            $categoryCounts[$item->id] = [
                'total' => $item->mycostumes->count(),
                'by_category' => $item->mycostumes->groupBy('mycostume_cate_id')->map->count()
            ];
        }

        $categories = MycostumeCate::orderBy('sort', 'asc')->get();

        return view('admin.mycostumes.index', compact('items', 'categories', 'categoryCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $items = Item::orderBy('item_name', 'asc')->get();
        $categories = MycostumeCate::orderBy('sort', 'asc')->get();
        $selectedItemId = $request->get('item_id');

        return view('admin.mycostumes.create', compact('items', 'categories', 'selectedItemId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'mycostume_cate_id' => 'required|exists:mycostume_cates,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'boolean',
        ]);

        $filename = 'mycostume_' . time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('mycostume', $filename);

        Mycostume::create([
            'item_id' => $request->item_id,
            'mycostume_cate_id' => $request->mycostume_cate_id,
            'name' => $request->name,
            'body' => $request->description,
            'filename' => $filename,
            'active' => $request->active ?? 1,
            'sort' => 0, // デフォルト値として0を設定
        ]);

        return redirect()->route('admin.mycostumes.cast', $request->item_id)
            ->with('success', 'マイコスチュームを追加しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Mycostume $mycostume)
    {
        return view('admin.mycostumes.show', compact('mycostume'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mycostume $mycostume)
    {
        $items = Item::orderBy('item_name', 'asc')->get();
        $categories = MycostumeCate::orderBy('sort', 'asc')->get();

        return view('admin.mycostumes.edit', compact('mycostume', 'items', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mycostume $mycostume)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'mycostume_cate_id' => 'required|exists:mycostume_cates,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'boolean',
        ]);

        $data = [
            'item_id' => $request->item_id,
            'mycostume_cate_id' => $request->mycostume_cate_id,
            'name' => $request->name,
            'body' => $request->description,
            'active' => $request->active ?? 1,
        ];

        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($mycostume->filename && Storage::exists('mycostume/' . $mycostume->filename)) {
                Storage::delete('mycostume/' . $mycostume->filename);
            }

            $filename = 'mycostume_' . time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('mycostume', $filename);
            $data['filename'] = $filename;
        }

        $mycostume->update($data);

        return redirect()->route('admin.mycostumes.cast', $mycostume->item_id)
            ->with('success', 'マイコスチュームを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mycostume $mycostume)
    {
        $itemId = $mycostume->item_id;

        // 画像ファイルを削除
        if ($mycostume->filename && Storage::exists('mycostume/' . $mycostume->filename)) {
            Storage::delete('mycostume/' . $mycostume->filename);
        }

        $mycostume->delete();

        return redirect()->route('admin.mycostumes.cast', $itemId)
            ->with('success', 'マイコスチュームを削除しました');
    }

    /**
     * 並び替え
     */
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            Mycostume::where('id', $item['id'])->update(['sort' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * 特定のキャストのマイコスチューム一覧を表示
     */
    public function cast($itemId)
    {
        $item = Item::findOrFail($itemId);
        $mycostumes = Mycostume::where('item_id', $itemId)
            ->with('category')
            ->orderBy('sort', 'asc')
            ->get();

        $categories = MycostumeCate::orderBy('sort', 'asc')->get();

        return view('admin.mycostumes.cast', compact('item', 'mycostumes', 'categories'));
    }
}
