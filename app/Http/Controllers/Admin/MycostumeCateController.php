<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MycostumeCate;
use Illuminate\Http\Request;

class MycostumeCateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $mycostumeCates = MycostumeCate::orderBy('sort', 'asc')->get();
        return view('admin.mycostume-cates.index', compact('mycostumeCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.mycostume-cates.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);


        MycostumeCate::create($request->all());

        return redirect()->route('admin.mycostume_cates.index')->with('success', 'オプションのカテゴリを作成しました');
    }


    public function edit($mycostume_cate)
    {

        $mycostumeCate = MycostumeCate::findOrFail($mycostume_cate);
// dd($mycostumeCate);
        return view('admin.mycostume-cates.edit', compact('mycostumeCate'));
    }


    public function update(Request $request, $mycostume_cate)
    {

// dd($mycostume_cate);

        $request->validate([
            'name' => 'required',
            'body' => 'nullable|string',
        ]);

        $mycostumeCate = MycostumeCate::findOrFail($mycostume_cate);
        $mycostumeCate->update($request->all());

        return redirect()->route('admin.mycostume_cates.index')->with('success', 'オプションカテゴリ更新しました');
    }


    public function destroy($id)
    {
        MycostumeCate::findOrFail($id)->delete();
        return redirect()->route('admin.mycostume_cates.index')->with('success', 'オプションカテゴリ削除しました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $mycostumecate = MycostumeCate::find($item['id']);
            $mycostumecate->timestamps = false;  // updated_atとcreated_atを無効化
            $mycostumecate->sort = $item['position'];
            $mycostumecate->save();
        }

        return response()->json(['success' => true]);
    }

}
