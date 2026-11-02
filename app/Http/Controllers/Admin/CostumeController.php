<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Costume;
use App\Models\CostumeCate;
use App\Services\ImageCompressor;
use Carbon\Carbon;

class CostumeController extends Controller
{
    //

    public function index()
    {
        $costumes = Costume::orderBy('sort', 'asc')->get();
        $costumeCates = CostumeCate::orderBy('sort', 'asc')->get();
        return view('admin.costumes.index', compact('costumes', 'costumeCates'));
    }



    // 新しいコスチュームの作成フォームを表示 (GET /admin/costumes/create)
    public function create()
    {
        //
        $costumeCates = CostumeCate::orderBy('sort', 'asc')->get();

        return view('admin.costumes.create', compact('costumeCates'));
    }

    // 新しいコスチュームを保存 (POST /admin/costumes)
    public function store(Request $request)
    {

        // dd($request);
        $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'integer',
            'cate' => 'nullable|integer',
            'active' => 'nullable|boolean', // これでチェックされたら1、されてなければ0
        ]);


        $validated['active'] = $request->has('active') ? 1 : 0;

        // 画像の保存処理
        // $filename = null;
        // if ($request->hasFile('filename')) {
        //     $filename = $request->file('filename')->store('upload/costume', 'public');
        // }

        $filename = null;
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = "cos_".time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にするためにタイムスタンプを追加
            // Laravelのファイルストレージを使って保存
            $file->storeAs('costume', $filename); // storage/app/costume に保存
            ImageCompressor::compressIfNeeded(storage_path('app/costume/' . $filename));
        }
        \Log::info('Saving costume image', [
            'filename' => $filename,
            'path' => storage_path('app/public/costume/' . $filename),
        ]);

        Costume::create([
            'name' => $request->name,
            'body' => $request->body,
            'filename' => $filename,
            'sort' => $request->sort ?? 0,
            'cate' => $request->cate ?? 0,
            'active' => $request->active ?? 0,
        ]);

        return redirect()->route('admin.costumes.index')->with('success', 'コスチュームが作成されました');
    }

    // コスチュームを表示 (GET /admin/costumes/{costume})
    public function show(Costume $costume)
    {
        return view('admin.costumes.show', compact('costume'));
    }

    // コスチューム編集フォームを表示 (GET /admin/costumes/{costume}/edit)
    public function edit(Costume $costume)
    {
        $costumeCates = CostumeCate::orderBy('sort', 'asc')->get();
        return view('admin.costumes.edit', compact('costume', 'costumeCates'));
    }

    // コスチュームを更新 (PUT /admin/costumes/{costume})
    public function update(Request $request, Costume $costume)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'integer',
            'cate' => 'nullable|integer', // これでチェックされたら1、されてなければ0
        ]);



        // 画像削除のチェックがある場合
        if ($request->has('delete_image') && $costume->filename) {
            if (Storage::exists('costume/' . $costume->filename)) {
                Storage::delete('costume/' . $costume->filename); // Laravel流の削除
            }
            $costume->filename = null;
        }

        // 古い画像の削除処理と新しい画像の保存処理
        $filename = $costume->filename; // 既存のファイル名を保持


        if ($request->hasFile('filename')) {
            // 古い画像が存在している場合は削除
            if ($filename && Storage::exists('costume/' . $filename)) {
                Storage::delete('costume/' . $filename); // Laravel流の削除
            }
            // 新しい画像の保存処理
            $file = $request->file('filename');
            $filename = 'costume_'.time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする
            $file->storeAs('costume', $filename); // 新しい画像を保存
            ImageCompressor::compressIfNeeded(storage_path('app/costume/' . $filename));
        }


        // dd($file);

        // dd($request);
        // コスチュームの更新
        $costume->update([
            'name' => $request->name,
            'body' => $request->body,
            'filename' => $filename,
            'sort' => $request->sort ?? 0,
            'active' => $request->active ?? 0,
            'cate' => $request->cate ?? 0,
        ]);

        return redirect()->route('admin.costumes.index')->with('success', 'コスチュームが更新されました');
    }


    // コスチュームを削除 (DELETE /admin/costumes/{costume})
    public function destroy($costume)
    {
        $costume = Costume::findOrFail($costume);

        // 画像が存在している場合は削除
        if ($costume->filename && Storage::exists('costume/' . $costume->filename)) {
            Storage::delete('costume/' . $costume->filename); // 古い画像を削除
        }

        // コスチュームデータの削除
        $costume->delete();

        return redirect()->route('admin.costumes.index')->with('success', 'コスチュームが削除されました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // コスチュームを並び替え順序に基づいて更新
            Costume::where('id', $item['id'])->update(['sort' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

}
