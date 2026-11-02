<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $profiles = Profile::orderBy('sort', 'asc')->get();

        return view('item.profiles.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('item.profiles.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        Profile::create($request->all());

        return redirect()->route('item.profiles.index')->with('success', 'オプション追加完了');
    }

    public function edit($id)
    {
        $profile = Profile::findOrFail($id);
        return view('item.profiles.edit', compact('profile'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $profile = Profile::findOrFail($id);
        $profile->update($request->all());

        return redirect()->route('item.profiles.index')->with('success', 'オプション更新完了');
    }

    public function destroy($id)
    {
        Profile::findOrFail($id)->delete();
        return redirect()->route('item.profiles.index')->with('success', 'オプション削除完了');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $profiles = Profile::find($item['id']);
            $profiles->timestamps = false;  // updated_atとcreated_atを無効化
            $profiles->sort = $item['position'];
            $profiles->save();
        }

        return response()->json(['success' => true]);
    }
}
