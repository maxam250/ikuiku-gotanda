<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MyoptionCate;
use Illuminate\Http\Request;

class MyoptionCateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $myoptionCates = MyoptionCate::orderBy('sort', 'asc')->get();
        return view('admin.myoption-cates.index', compact('myoptionCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.myoption-cates.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);


        MyoptionCate::create($request->all());

        return redirect()->route('admin.myoption_cates.index')->with('success', 'オプションのカテゴリを作成しました');
    }


    public function edit($myoption_cate)
    {

        $myoptionCate = MyoptionCate::findOrFail($myoption_cate);
// dd($myoptionCate);
        return view('admin.myoption-cates.edit', compact('myoptionCate'));
    }


    public function update(Request $request, $myoption_cate)
    {

// dd($myoption_cate);

        $request->validate([
            'name' => 'required',
            'body' => 'nullable|string',
        ]);

        $myoptionCate = MyoptionCate::findOrFail($myoption_cate);
        $myoptionCate->update($request->all());

        return redirect()->route('admin.myoption_cates.index')->with('success', 'オプションカテゴリ更新しました');
    }


    public function destroy($id)
    {
        MyoptionCate::findOrFail($id)->delete();
        return redirect()->route('admin.myoption_cates.index')->with('success', 'オプションカテゴリ削除しました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $myoptioncate = MyoptionCate::find($item['id']);
            $myoptioncate->timestamps = false;  // updated_atとcreated_atを無効化
            $myoptioncate->sort = $item['position'];
            $myoptioncate->save();
        }

        return response()->json(['success' => true]);
    }

}
