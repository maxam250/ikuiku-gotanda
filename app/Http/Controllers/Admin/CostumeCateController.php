<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostumeCate;
use Illuminate\Http\Request;

class CostumeCateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $costumeCates = CostumeCate::orderBy('sort', 'asc')->get();
        return view('admin.costume_cates.index', compact('costumeCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.costume_cates.create');
    }


    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
        ]);


        CostumeCate::create($request->all());

        return redirect()->route('admin.costume_cates.index')->with('success', 'のカテゴリを作成しました');
    }


    public function edit($costume_cate)
    {

        $costumeCate = CostumeCate::findOrFail($costume_cate);
// dd($costumeCate);
        return view('admin.costume_cates.edit', compact('costumeCate'));
    }


    public function update(Request $request, $costume_cate)
    {

// dd($costume_cate);

        $request->validate([
            'active' => ['required', 'in:0,1'],
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
        ]);

        $costumeCate = CostumeCate::findOrFail($costume_cate);
        $costumeCate->update($request->all());

        return redirect()->route('admin.costume_cates.index')->with('success', 'カテゴリ更新しました');
    }


    public function destroy($id)
    {
        CostumeCate::findOrFail($id)->delete();
        return redirect()->route('admin.costume_cates.index')->with('success', 'カテゴリ削除しました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $costumecate = CostumeCate::find($item['id']);
            $costumecate->timestamps = false;  // updated_atとcreated_atを無効化
            $costumecate->sort = $item['position'];
            $costumecate->save();
        }

        return response()->json(['success' => true]);
    }

}
