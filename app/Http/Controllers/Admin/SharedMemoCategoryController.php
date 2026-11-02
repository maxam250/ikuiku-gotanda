<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SharedMemoCategory;
use Illuminate\Http\Request;

class SharedMemoCategoryController extends Controller
{
    public function index()
    {
        $categories = SharedMemoCategory::orderBy('sort')->orderBy('created_at', 'desc')->get();
        return view('admin.shared_memos.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.shared_memos.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort'        => 'nullable|integer',
        ]);

        SharedMemoCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
            'sort'        => $request->sort ?? 0,
        ]);

        return redirect()->route('admin.shared_memo_categories.index')
            ->with('success', 'カテゴリが作成されました');
    }

    public function edit(SharedMemoCategory $sharedMemoCategory)
    {
        return view('admin.shared_memos.categories.edit', compact('sharedMemoCategory'));
    }

    public function update(Request $request, SharedMemoCategory $sharedMemoCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort'        => 'nullable|integer',
        ]);

        $sharedMemoCategory->update([
            'name'        => $request->name,
            'description' => $request->description,
            'sort'        => $request->sort ?? 0,
        ]);

        return redirect()->route('admin.shared_memo_categories.index')
            ->with('success', 'カテゴリが更新されました');
    }

    public function destroy(SharedMemoCategory $sharedMemoCategory)
    {
        $sharedMemoCategory->delete();
        return redirect()->route('admin.shared_memo_categories.index')
            ->with('success', 'カテゴリが削除されました');
    }
}
