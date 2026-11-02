<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mycostume;
use App\Models\MycostumeCate;
// use App\Models\Item;
use Illuminate\Http\Request;

class MycostumeController_old extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $mycostumes = Mycostume::orderBy('cate', 'asc')->orderBy('sort', 'asc')->get();


        $mycostumeCates = MycostumeCate::orderBy('sort', 'asc')->get();

        return view('admin.mycostumes.index', compact('mycostumes', 'mycostumeCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.mycostumes.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cate' => 'required',
            'title' => 'required',
        ]);

        Mycostume::create($request->all());

        return redirect()->route('admin.mycostumes.index')->with('success', 'オプション追加完了');
    }

    public function edit($id)
    {
        $mycostume = Mycostume::findOrFail($id);
        $mycostumeCates = MycostumeCate::orderBy('sort', 'asc')->get();
        return view('admin.mycostumes.edit', compact('mycostume', 'mycostumeCates'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cate' => 'required',
            'title' => 'required',
        ]);

        $mycostume = Mycostume::findOrFail($id);
        $mycostume->update($request->all());

        return redirect()->route('admin.mycostumes.index')->with('success', 'オプション更新完了');
    }

    public function destroy($id)
    {
        Mycostume::findOrFail($id)->delete();
        return redirect()->route('admin.mycostumes.index')->with('success', 'オプション削除完了');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $mycostumes = Mycostume::find($item['id']);
            $mycostumes->timestamps = false;  // updated_atとcreated_atを無効化
            $mycostumes->sort = $item['position'];
            $mycostumes->save();
        }

        return response()->json(['success' => true]);
    }
}
