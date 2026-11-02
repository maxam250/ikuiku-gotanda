<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\IconItem;
use Illuminate\Http\Request;

class IconItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $icon_items = IconItem::orderBy('sort', 'asc')->get();

        return view('item.icon_items.index', compact('icon_items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('item.icon_items.index');
    }


    public function store(Request $request)
    {


        $request->validate([
            'title' => 'required',
            'filename' => 'nullable|image|max:400',
        ]);

        // 新しい画像の保存処理
        $filename = null;
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = 'icon_'.time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする

            $file->move(public_path('uploads/icon_item'), $filename); // 新しい画像を保存
        }


        // データベースに保存するデータを用意

        $iconItemData = [
            'title' => $request->input('title'),
            'filename' => $filename,
        ];


        // データベースに保存
        // IconItem::create($request->all());
        IconItem::create($iconItemData);



       return redirect()->route('item.icon_items.index')->with('success', 'キャストアイコン追加完了');
    }


    public function edit($icon_item)
    {
        $icon_item = IconItem::findOrFail($icon_item);
        return view('item.icon_items.edit', compact('icon_item'));
    }

    public function update(Request $request, IconItem $icon_item)
    {
        $request->validate([
            'title' => 'required',
            'filename' => 'nullable|image|max:600',
        ]);

        // 画像削除のチェックがある場合
        if ($request->has('delete_image') && $icon_item->filename) {
            if (file_exists(public_path('uploads/icon_item/' . $icon_item->filename))) {
                unlink(public_path('uploads/icon_item/' . $icon_item->filename)); // 画像ファイルの削除
            }
            $icon_item->filename = null; // データベースのfilenameカラムをnullにする
        }


        // 古い画像の削除処理と新しい画像の保存処理
        $filename = $icon_item->filename; // 既存のファイル名を保持


        if ($request->hasFile('filename')) {
            // 古い画像が存在している場合は削除
            if ($filename && file_exists(public_path('uploads/icon_item/' . $filename))) {
                unlink(public_path('uploads/icon_item/' . $filename)); // 古い画像を削除
            }

            // 新しい画像の保存処理
            $file = $request->file('filename');
            $filename = 'icon_'.time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする
            $file->move(public_path('uploads/icon_item'), $filename); // 新しい画像を保存
        }


        // dd($file);

        // dd($request);
        // キャストアイコンの更新
        $icon_item->update([
            'title' => $request->title,
            'filename' => $filename,
        ]);

        return redirect()->route('item.icon_items.index')->with('success', 'キャストアイコン更新完了');
    }

    public function destroy($id)
    {
        IconItem::findOrFail($id)->delete();
        return redirect()->route('item.icon_items.index')->with('success', 'キャストアイコン削除完了');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $icon_items = IconItem::find($item['id']);
            $icon_items->timestamps = false;  // updated_atとcreated_atを無効化
            $icon_items->sort = $item['position'];
            $icon_items->save();
        }

        return response()->json(['success' => true]);
    }
}
