<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Interview;
use Illuminate\Http\Request;

class ItemInterviewController extends Controller
{
    //
    public function edit($itemId)
    {
        $item = Item::findOrFail($itemId);
        $interviews = Interview::all(); // 全インタビュー
        $selectedInterviews = $item->interviews()->pluck('id')->toArray(); // キャストに設定されているインタビューID
        return view('admin.items.interviews.edit', compact('item', 'interviews', 'selectedInterviews'));
    }

    public function update(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        // チェックされたインタビューを同期
        $item->interviews()->sync($request->input('interviews', []));

        return redirect()->route('items.interviews.edit', $itemId)->with('success', 'Q&Aのアップデート完了');
    }
}
