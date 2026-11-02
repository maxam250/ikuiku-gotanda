<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentCate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentCateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $equipmentCates = EquipmentCate::orderBy('sort', 'asc')->get();
        return view('admin.equipment_cates.index', compact('equipmentCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.equipment_cates.create');
    }


    public function store(Request $request)
    {

        $request->validate([
            'active' => ['required', 'in:0,1'],
            'title' => 'required|string|max:255',
        ]);


        EquipmentCate::create($request->all());

        return redirect()->route('admin.equipment_cates.index')->with('success', 'のカテゴリを作成しました');
    }


    public function edit($equipment_cate)
    {

        $equipmentCate = EquipmentCate::findOrFail($equipment_cate);
// dd($equipmentCate);
        return view('admin.equipment_cates.edit', compact('equipmentCate'));
    }


    public function update(Request $request, $equipment_cate)
    {

// dd($equipment_cate);

        $request->validate([
            'active' => ['required', 'in:0,1'],
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
        ]);

        $equipmentCate = EquipmentCate::findOrFail($equipment_cate);
        $equipmentCate->update($request->all());

        return redirect()->route('admin.equipment_cates.index')->with('success', 'カテゴリ更新しました');
    }


    public function destroy($id)
    {
        EquipmentCate::findOrFail($id)->delete();
        return redirect()->route('admin.equipment_cates.index')->with('success', 'カテゴリ削除しました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $equipmentcate = EquipmentCate::find($item['id']);
            $equipmentcate->timestamps = false;  // updated_atとcreated_atを無効化
            $equipmentcate->sort = $item['position'];
            $equipmentcate->save();
        }

        return response()->json(['success' => true]);
    }

}
