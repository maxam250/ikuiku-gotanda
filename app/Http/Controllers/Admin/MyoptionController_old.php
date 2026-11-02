<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Myoption;
use App\Models\MyoptionCate;
// use App\Models\Item;
use Illuminate\Http\Request;

class MyoptionController_old extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $myoptions = Myoption::orderBy('cate', 'asc')->orderBy('sort', 'asc')->get();


        $myoptionCates = MyoptionCate::orderBy('sort', 'asc')->get();

        return view('admin.myoptions.index', compact('myoptions', 'myoptionCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.myoptions.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cate' => 'required',
            'title' => 'required',
        ]);

        Myoption::create($request->all());

        return redirect()->route('admin.myoptions.index')->with('success', 'オプション追加完了');
    }

    public function edit($id)
    {
        $myoption = Myoption::findOrFail($id);
        $myoptionCates = MyoptionCate::orderBy('sort', 'asc')->get();
        return view('admin.myoptions.edit', compact('myoption', 'myoptionCates'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cate' => 'required',
            'title' => 'required',
        ]);

        $myoption = Myoption::findOrFail($id);
        $myoption->update($request->all());

        return redirect()->route('admin.myoptions.index')->with('success', 'オプション更新完了');
    }

    public function destroy($id)
    {
        Myoption::findOrFail($id)->delete();
        return redirect()->route('admin.myoptions.index')->with('success', 'オプション削除完了');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $myoptions = Myoption::find($item['id']);
            $myoptions->timestamps = false;  // updated_atとcreated_atを無効化
            $myoptions->sort = $item['position'];
            $myoptions->save();
        }

        return response()->json(['success' => true]);
    }
}
