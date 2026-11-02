<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopGroupController extends Controller
{
    public function index()
    {
        $shopGroups = ShopGroup::orderBy('sort', 'asc')->orderBy('id', 'asc')->get();
        return view('admin.shop-groups.index', compact('shopGroups'));
    }

    public function create()
    {
        return view('admin.shop-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'url'       => 'nullable|url|max:255',
            'image_url' => 'nullable|url|max:255',
            'filename'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['name', 'url', 'sort', 'image_url']);
        $data['is_publish'] = $request->has('is_publish') ? 1 : 0;
        $data['sort']       = $request->input('sort', 0);

        if ($request->hasFile('filename')) {
            $data['filename'] = $request->file('filename')->store('shop-groups', 'public');
        }

        ShopGroup::create($data);

        return redirect()->route('admin.shop-groups.index')->with('success', '系列店を追加しました');
    }

    public function edit(string $id)
    {
        $shopGroup = ShopGroup::findOrFail($id);
        return view('admin.shop-groups.edit', compact('shopGroup'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'url'       => 'nullable|url|max:255',
            'image_url' => 'nullable|url|max:255',
            'filename'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $shopGroup = ShopGroup::findOrFail($id);

        $data = $request->only(['name', 'url', 'sort', 'image_url']);
        $data['is_publish'] = $request->has('is_publish') ? 1 : 0;
        $data['sort']       = $request->input('sort', 0);

        if ($request->hasFile('filename')) {
            if ($shopGroup->filename) {
                Storage::disk('public')->delete($shopGroup->filename);
            }
            $data['filename'] = $request->file('filename')->store('shop-groups', 'public');
        }

        $shopGroup->update($data);

        return redirect()->route('admin.shop-groups.index')->with('success', '系列店を更新しました');
    }

    public function destroy(string $id)
    {
        $shopGroup = ShopGroup::findOrFail($id);

        if ($shopGroup->filename) {
            Storage::disk('public')->delete($shopGroup->filename);
        }

        $shopGroup->delete();
        return redirect()->route('admin.shop-groups.index')->with('success', '系列店を削除しました');
    }

    // Ajax 並び順更新
    public function sort(Request $request)
    {
        foreach ($request->input('order', []) as $item) {
            ShopGroup::where('id', $item['id'])->update(['sort' => $item['position']]);
        }
        return response()->json(['success' => true]);
    }
}
