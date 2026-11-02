<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RankCate;
use Illuminate\Http\Request;

class RankCateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $rankCates = RankCate::orderBy('sort', 'asc')->get();
        return view('admin.rank-cates.index', compact('rankCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.rank-cates.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);


        RankCate::create($request->all());

        return redirect()->route('admin.rank_cates.index')->with('success', 'オプションのカテゴリを作成しました');
    }


    public function edit($rank_cate)
    {

        $rankCate = RankCate::findOrFail($rank_cate);
// dd($rankCate);
        return view('admin.rank-cates.edit', compact('rankCate'));
    }


    public function update(Request $request, $rank_cate)
    {

// dd($rank_cate);

        $request->validate([
            'active' => 'required|in:0,1',
            'title' => 'required',
            'body' => 'nullable|string',
        ]);

        $rankCate = RankCate::findOrFail($rank_cate);
        $rankCate->update($request->all());

        return redirect()->route('admin.rank_cates.index')->with('success', 'オプションカテゴリ更新しました');
    }


    public function destroy($id)
    {
        RankCate::findOrFail($id)->delete();
        return redirect()->route('admin.rank_cates.index')->with('success', 'オプションカテゴリ削除しました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $rankcate = RankCate::find($item['id']);
            $rankcate->timestamps = false;  // updated_atとcreated_atを無効化
            $rankcate->sort = $item['position'];
            $rankcate->save();
        }

        return response()->json(['success' => true]);
    }

}
