<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SharedMemo;
use App\Models\SharedMemoCategory;
use App\Models\Staff;
use Illuminate\Http\Request;

class SharedMemoController extends Controller
{
    public function index(Request $request)
    {
        $query = SharedMemo::with(['category', 'staff']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('date_from')) {
            $query->where('memo_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('memo_date', '<=', $request->date_to);
        }

        $memos      = $query->orderBy('memo_date', 'desc')->paginate(20);
        $categories = SharedMemoCategory::orderBy('sort')->get();
        $staffs     = Staff::where('is_active', 1)->orderBy('sort')->orderBy('name')->get();

        return view('admin.shared_memos.index', compact('memos', 'categories', 'staffs'));
    }

    public function create()
    {
        $categories = SharedMemoCategory::orderBy('sort')->get();
        $staffs     = Staff::where('is_active', 1)->orderBy('sort')->orderBy('name')->get();
        return view('admin.shared_memos.create', compact('categories', 'staffs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:shared_memo_categories,id',
            'staff_id'    => 'nullable|exists:staffs,id',
            'memo_date'   => 'required|date',
            'title'       => 'nullable|string|max:255',
            'body'        => 'nullable|string',
            'url'         => 'nullable|url|max:255',
        ]);

        SharedMemo::create([
            'category_id' => $request->category_id ?: null,
            'staff_id'    => $request->staff_id ?: null,
            'memo_date'   => $request->memo_date,
            'title'       => $request->title,
            'body'        => $request->body,
            'url'         => $request->url,
        ]);

        return redirect()->route('admin.shared_memos.index')
            ->with('success', '共有メモが作成されました');
    }

    public function edit(SharedMemo $sharedMemo)
    {
        $categories = SharedMemoCategory::orderBy('sort')->get();
        $staffs     = Staff::where('is_active', 1)->orderBy('sort')->orderBy('name')->get();
        return view('admin.shared_memos.edit', compact('sharedMemo', 'categories', 'staffs'));
    }

    public function update(Request $request, SharedMemo $sharedMemo)
    {
        $request->validate([
            'category_id' => 'nullable|exists:shared_memo_categories,id',
            'staff_id'    => 'nullable|exists:staffs,id',
            'memo_date'   => 'required|date',
            'title'       => 'nullable|string|max:255',
            'body'        => 'nullable|string',
            'url'         => 'nullable|url|max:255',
        ]);

        $sharedMemo->update([
            'category_id' => $request->category_id ?: null,
            'staff_id'    => $request->staff_id ?: null,
            'memo_date'   => $request->memo_date,
            'title'       => $request->title,
            'body'        => $request->body,
            'url'         => $request->url,
        ]);

        return redirect()->route('admin.shared_memos.index')
            ->with('success', '共有メモが更新されました');
    }

    public function destroy(SharedMemo $sharedMemo)
    {
        $sharedMemo->delete();
        return redirect()->route('admin.shared_memos.index')
            ->with('success', '共有メモが削除されました');
    }
}
