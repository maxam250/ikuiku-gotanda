<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemCategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::withCount('items')->orderBy('sort')->get();
        return view('admin.item-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:50',
            'icon_image' => 'nullable|image|mimes:jpeg,png,gif,svg,webp|max:1024',
            'color'      => 'required|string|max:20',
        ]);

        $maxSort = ItemCategory::max('sort') ?? 0;

        $data = [
            'name'  => $request->name,
            'color' => $request->color,
            'sort'  => $maxSort + 1,
        ];

        if ($request->hasFile('icon_image')) {
            $file = $request->file('icon_image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $data['icon_image'] = $file->storeAs('item-categories', $filename, 'public');
        }

        ItemCategory::create($data);

        return redirect()->route('admin.item-categories.index')
            ->with('success', 'カテゴリを作成しました');
    }

    public function edit(ItemCategory $itemCategory)
    {
        return view('admin.item-categories.edit', compact('itemCategory'));
    }

    public function update(Request $request, ItemCategory $itemCategory)
    {
        $request->validate([
            'name'       => 'required|string|max:50',
            'icon_image' => 'nullable|image|mimes:jpeg,png,gif,svg,webp|max:1024',
            'color'      => 'required|string|max:20',
        ]);

        $data = [
            'name'  => $request->name,
            'color' => $request->color,
        ];

        if ($request->hasFile('icon_image')) {
            if ($itemCategory->icon_image) {
                Storage::disk('public')->delete($itemCategory->icon_image);
            }
            $file = $request->file('icon_image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $data['icon_image'] = $file->storeAs('item-categories', $filename, 'public');
        }

        $itemCategory->update($data);

        return redirect()->route('admin.item-categories.index')
            ->with('success', 'カテゴリを更新しました');
    }

    public function destroy(ItemCategory $itemCategory)
    {
        if ($itemCategory->icon_image) {
            Storage::disk('public')->delete($itemCategory->icon_image);
        }

        $itemCategory->delete();

        return redirect()->route('admin.item-categories.index')
            ->with('success', 'カテゴリを削除しました');
    }

    public function sort(Request $request)
    {
        foreach ($request->input('order', []) as $item) {
            $cat = ItemCategory::find($item['id']);
            if ($cat) {
                $cat->timestamps = false;
                $cat->sort = $item['position'];
                $cat->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
