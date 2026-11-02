<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Http\Request;

class ItemProfileController extends Controller
{
    //
    public function edit($itemId)
    {
        $item = Item::findOrFail($itemId);
        $profiles = Profile::all(); // 全プロフィール
        $selectedProfiles = $item->profiles()->pluck('id')->toArray(); // キャストに設定されているプロフィールID
        return view('admin.items.profiles.edit', compact('item', 'profiles', 'selectedProfiles'));
    }

    public function update(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        // チェックされたプロフィールを同期
        $item->profiles()->sync($request->input('profiles', []));

        return redirect()->route('items.profiles.edit', $itemId)->with('success', 'プロフィールのアップデート完了');
    }
}
