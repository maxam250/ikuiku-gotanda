<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\Option;
use Illuminate\Http\Request;

class CastOptionController extends Controller
{
    //
    public function edit($castId)
    {
        $cast = Cast::findOrFail($castId);
        $options = Option::all(); // 全オプション
        $selectedOptions = $cast->options()->pluck('id')->toArray(); // キャストに設定されているオプションID

        return view('item.casts.options.edit', compact('cast', 'options', 'selectedOptions'));
    }

    public function update(Request $request, $castId)
    {
        $cast = Cast::findOrFail($castId);

        // チェックされたオプションを同期
        // options_submitted が存在する場合のみ sync を実行する。
        // チェックボックスは未選択時に送信されないため、このフラグがないと
        // フォームを経由しない不正なリクエストで全件削除されるリスクがある。
        if ($request->has('options_submitted')) {
            $cast->options()->sync($request->input('options', []));
        }

        return redirect()->route('casts.options.edit', $castId)->with('success', 'Options updated successfully');
    }
}
