<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\OptionPay;
use Illuminate\Http\Request;

class ItemOptionPayController extends Controller
{

    public function edit($itemId)
    {
        $item = Item::findOrFail($itemId);
        $option_pays = OptionPay::all(); // 全オプション
        $selectedOptionPays = $item->option_pays()->pluck('id')->toArray(); // キャストに設定されているオプションID

        return view('item.items.option_pays.edit', compact('item', 'option_pays', 'selectedOptionPays'));
    }

    public function update(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        // チェックされたオプションを同期
        $item->option_pays()->sync($request->input('option_pays', []));

        return redirect()->route('items.option_pays.edit', $itemId)->with('success', 'オプションPAYのアップデート完了');
    }
}
