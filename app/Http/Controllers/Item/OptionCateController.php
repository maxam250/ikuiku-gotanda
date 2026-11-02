<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\OptionCate;
use Illuminate\Http\Request;

class OptionCateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $optionCates = OptionCate::orderBy('sort', 'asc')->get();
        return view('item.option-cates.index', compact('optionCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('item.option-cates.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);


        OptionCate::create($request->all());

        return redirect()->route('item.option_cates.index')->with('success', 'オプションのカテゴリを作成しました');
    }


    public function edit($option_cate)
    {

        $optionCate = OptionCate::findOrFail($option_cate);
// dd($optionCate);
        return view('item.option-cates.edit', compact('optionCate'));
    }


    public function update(Request $request, $option_cate)
    {

// dd($option_cate);

        $request->validate([
            'title' => 'required',
            'body' => 'nullable|string',
        ]);

        $optionCate = OptionCate::findOrFail($option_cate);
        $optionCate->update($request->all());

        return redirect()->route('item.option_cates.index')->with('success', 'オプションカテゴリ更新しました');
    }


    public function destroy($id)
    {
        OptionCate::findOrFail($id)->delete();
        return redirect()->route('item.option_cates.index')->with('success', 'オプションカテゴリ削除しました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $optioncate = OptionCate::find($item['id']);
            $optioncate->timestamps = false;  // updated_atとcreated_atを無効化
            $optioncate->sort = $item['position'];
            $optioncate->save();
        }

        return response()->json(['success' => true]);
    }

}
