<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Myoption;
use App\Models\MyoptionCate;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MyoptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // キャストごとにマイオプションを取得
        $items = Item::with(['myoptions' => function ($query) {
            $query->orderBy('sort', 'asc');
        }])->whereHas('myoptions')->orderBy('item_name', 'asc')->get();

        // カテゴリ別のカウントを計算
        $categoryCounts = [];
        foreach ($items as $item) {
            $categoryCounts[$item->id] = [
                'total' => $item->myoptions->count(),
                'by_category' => $item->myoptions->groupBy('myoption_cate_id')->map->count()
            ];
        }

        $categories = MyoptionCate::orderBy('sort', 'asc')->get();

        return view('admin.myoptions.index', compact('items', 'categories', 'categoryCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $items = Item::orderBy('item_name', 'asc')->get();
        $categories = MyoptionCate::orderBy('sort', 'asc')->get();
        $selectedItemId = $request->get('item_id');

        return view('admin.myoptions.create', compact('items', 'categories', 'selectedItemId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'myoption_cate_id' => 'required|exists:myoption_cates,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'boolean',
        ]);

        $filename = 'myoption_' . time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('myoption', $filename);

        Myoption::create([
            'item_id' => $request->item_id,
            'myoption_cate_id' => $request->myoption_cate_id,
            'name' => $request->name,
            'body' => $request->description,
            'filename' => $filename,
            'active' => $request->active ?? 1,
            'sort' => 0, // デフォルト値として0を設定
        ]);

        return redirect()->route('admin.myoptions.cast', $request->item_id)
            ->with('success', 'マイオプションを追加しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Myoption $myoption)
    {
        return view('admin.myoptions.show', compact('myoption'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Myoption $myoption)
    {
        $items = Item::orderBy('item_name', 'asc')->get();
        $categories = MyoptionCate::orderBy('sort', 'asc')->get();

        return view('admin.myoptions.edit', compact('myoption', 'items', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Myoption $myoption)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'myoption_cate_id' => 'required|exists:myoption_cates,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'boolean',
        ]);

        $data = [
            'item_id' => $request->item_id,
            'myoption_cate_id' => $request->myoption_cate_id,
            'name' => $request->name,
            'body' => $request->description,
            'active' => $request->active ?? 1,
        ];

        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($myoption->filename && Storage::exists('myoption/' . $myoption->filename)) {
                Storage::delete('myoption/' . $myoption->filename);
            }

            $filename = 'myoption_' . time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('myoption', $filename);
            $data['filename'] = $filename;
        }

        $myoption->update($data);

        return redirect()->route('admin.myoptions.cast', $myoption->item_id)
            ->with('success', 'マイオプションを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Myoption $myoption)
    {
        $itemId = $myoption->item_id;

        // 画像ファイルを削除
        if ($myoption->filename && Storage::exists('myoption/' . $myoption->filename)) {
            Storage::delete('myoption/' . $myoption->filename);
        }

        $myoption->delete();

        return redirect()->route('admin.myoptions.cast', $itemId)
            ->with('success', 'マイオプションを削除しました');
    }

    /**
     * 並び替え
     */
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            Myoption::where('id', $item['id'])->update(['sort' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * 特定のキャストのマイオプション一覧を表示
     */
    public function cast($itemId)
    {
        $item = Item::findOrFail($itemId);
        $myoptions = Myoption::where('item_id', $itemId)
            ->with('category')
            ->orderBy('sort', 'asc')
            ->get();

        $categories = MyoptionCate::orderBy('sort', 'asc')->get();

        return view('admin.myoptions.cast', compact('item', 'myoptions', 'categories'));
    }
}
