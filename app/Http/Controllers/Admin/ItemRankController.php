<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Rank;
use Illuminate\Http\Request;

class ItemRankController extends Controller
{
    //
    public function edit($itemId)
    {
        $item = Item::findOrFail($itemId);
        $ranks = Rank::all(); // 全オプション
        $selectedRanks = $item->ranks()->pluck('id')->toArray(); // キャストに設定されているオプションID

        return view('admin.items.ranks.edit', compact('item', 'ranks', 'selectedRanks'));
    }

    public function update(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        // チェックされたオプションを同期
        $item->ranks()->sync($request->input('ranks', []));

        return redirect()->route('items.ranks.edit', $itemId)->with('success', 'オプションのアップデート完了');
    }
}
