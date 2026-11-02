<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\IconItem;
use Illuminate\Http\Request;

class ItemIconItemController extends Controller
{
    //
    public function edit($itemId)
    {
        $item = Item::findOrFail($itemId);
        $icon_items = IconItem::all(); // 全プロフィール
        $selectedIconItems = $item->icon_items()->pluck('id')->toArray(); // キャストに設定されているプロフィールID

        return view('admin.items.icon_items.edit', compact('item', 'icon_items', 'selectedIconItems'));
    }

    public function update(Request $request, $itemId)
    {
+
        $item = Item::findOrFail($itemId);

        // チェックされたプロフィールを同期
        $item->icon_items()->sync($request->input('icon_items', []));

        return redirect()->route('items.icon_items.edit', $itemId)->with('success', 'プロフィールのアップデート完了');
    }
}
