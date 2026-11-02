<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Link;
use Carbon\Carbon;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $links = Link::orderBy('position', 'asc')->orderBy('sort', 'asc')->get();
        return view('item.links.index', compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('item.links.create');
    }

    
    public function store(Request $request)
    {

        // dd($request);
        $request->validate([
            'title' => 'required|string|max:255',
            'fullcode' => 'nullable|string',
            'filename' => 'nullable|image|max:2048',
            'sort' => 'integer',
            'position' => 'nullable|integer',
            'link' => 'nullable|string',
            'active' => 'nullable|boolean', // これでチェックされたら1、されてなければ0
        ]);

        $validated['active'] = $request->has('active') ? 1 : 0;


        // 画像の保存処理
 
        $filename = null;
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = "links_".time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にするためにタイムスタンプを追加
            $file->move(public_path('uploads/link'), $filename); // 画像を指定ディレクトリに保存
        }


        Link::create([
            'title' => $request->title,
            'fullcode' => $request->fullcode,
            'filename' => $filename,
            'active' => $request->active ? 1 : 0,
            'sort' => $request->sort ?? 0,
            'position' => $request->position ?? 0,
            'link' => $request->link,
        ]);

        return redirect()->route('item.links.index')->with('success', 'リンクが作成されました');
    }

    // リンクを表示 (GET /item/links/{links})
    public function show(Link $link)
    {
        return view('item.links.show', compact('links'));
    }

    // リンク編集フォームを表示 (GET /item/links/{links}/edit)
    public function edit(Link $link)
    {
        return view('item.links.edit', compact('link'));
    }

    // リンクを更新 (PUT /item/links/{links})
    public function update(Request $request, Link $link)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'fullcode' => 'nullable|string',
            'filename' => 'nullable|image|max:2048',
            'sort' => 'integer',
            'position' => 'nullable|integer',
            'link' => 'nullable|string',
            'active' => 'nullable|boolean', // これでチェックされたら1、されてなければ0
        ]);

        $validated['active'] = $request->has('active') ? 1 : 0;

        // 画像削除のチェックがある場合
        if ($request->has('delete_image') && $link->filename) {
            if (file_exists(public_path('uploads/link/' . $link->filename))) {
                unlink(public_path('uploads/link/' . $link->filename)); // 画像ファイルの削除
            }
            $link->filename = null; // データベースのfilenameカラムをnullにする
        }


        // 古い画像の削除処理と新しい画像の保存処理
        $filename = $link->filename; // 既存のファイル名を保持


        if ($request->hasFile('filename')) {
            // 古い画像が存在している場合は削除
            if ($filename && file_exists(public_path('uploads/link/' . $filename))) {
                unlink(public_path('uploads/link/' . $filename)); // 古い画像を削除
            }

            // 新しい画像の保存処理
            $file = $request->file('filename');
            $filename = 'links_'.time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする
            $file->move(public_path('uploads/link'), $filename); // 新しい画像を保存
        }


        // dd($file);

        // dd($request);
        // リンクの更新
        $link->update([
            'title' => $request->title,
            'fullcode' => $request->fullcode,
            'filename' => $filename,
            'active' => $request->active ? 1 : 0,
            'sort' => $request->sort ?? 0,
            'position' => $request->position ?? 0,
            'link' => $request->link,
        ]);

        return redirect()->route('item.links.index')->with('success', 'リンクが更新されました');
    }


    // リンクを削除 (DELETE /item/links/{links})
    public function destroy($link)
    {
        $links = Link::findOrFail($link);

        // 画像が存在している場合は削除
        if ($links->filename && file_exists(public_path('uploads/link/' . $links->filename))) {
            unlink(public_path('uploads/link/' . $links->filename)); // 古い画像を削除
        }

        // リンクデータの削除
        $links->delete();

        return redirect()->route('item.links.index')->with('success', 'リンクが削除されました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新
            
            // timestampsの自動更新を無効化して、並び替えを保存
            $links = Link::find($item['id']);
            $links->timestamps = false;  // updated_atとcreated_atを無効化
            $links->sort = $item['position'];
            $links->save();
        }

        return response()->json(['success' => true]);
    }
}
