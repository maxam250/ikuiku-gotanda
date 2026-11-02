<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //ViewComposerServiceProviderでshopsを使用しているため
        $shops2 = Shop::all();
        return view('admin.shops.index', compact('shops2'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.shops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'shop_name' => 'required',
            'shop_active' => 'nullable|boolean', // これでチェックされたら1、されてなければ0
        ]);

        $validated['shop_active'] = $request->has('shop_active') ? 1 : 0;

        Shop::create($request->all());

        return redirect()->route('admin.shops.index')->with('success', '店舗追加完了');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $shop = Shop::findOrFail($id);

        return view('admin.shops.edit', compact('shop'));
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, $id)
     {

         //　バリデータ
         $request->validate([
             'shop_name' => 'required|string|max:255',
             'shop_idname' => 'required|string|max:255',
             'shop_domain' => 'nullable|string|max:255',
             'shop_mail' => 'nullable|string|email|max:255|unique:shops,shop_mail,' . $id,
             'shop_mail2' => 'nullable|string|email|max:255|unique:shops,shop_mail2,' . $id,
         ]);

         // 項目の取得
         $shop = Shop::findOrFail($id);

         // フォームからのデータをユーザーに一括で代入
         $shop->fill($request->except('shop'));

         // checkboxをon→0/1に変換
         $shop->shop_active = $request->has('shop_active') ? 1 : 0;

         $shop->save();

         return redirect()->route('admin.shops.edit',['shop' => $id])->with('success', 'アップデート成功');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();
        return redirect()->route('admin.shops.index')->with('success', '店舗を削除しました');
    }
}
