<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Option;
use Illuminate\Http\Request;

class ItemOptionController extends Controller
{
    //
    public function edit($itemId)
    {
        $item = Item::findOrFail($itemId);
        $options = Option::all(); // 全オプション
        $selectedOptions = $item->options()->pluck('id')->toArray(); // キャストに設定されているオプションID

        return view('item.items.options.edit', compact('item', 'options', 'selectedOptions'));
    }

    public function update(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        // チェックされたオプションを同期
        $item->options()->sync($request->input('options', []));

        return redirect()->route('items.options.edit', $itemId)->with('success', 'オプションのアップデート完了');
    }
}
