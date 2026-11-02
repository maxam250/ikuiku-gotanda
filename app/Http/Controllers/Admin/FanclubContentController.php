<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FanclubContent;
use App\Models\FanclubContentCategory;
use App\Models\Item;
use App\Services\Fanclub\FanclubContentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FanclubContentController extends Controller
{
    public function __construct(
        private readonly FanclubContentService $contentService
    ) {
    }

    public function index()
    {
        $contents = FanclubContent::query()
            ->with(['cast'])
            ->withCount('children')
            ->latest()
            ->paginate(30);

        if (request()->expectsJson()) {
            return response()->json($contents);
        }

        return view('admin.fanclubs.contents.index', compact('contents'));
    }

    public function create()
    {
        $casts = Item::query()
            ->selectable()
            ->whereHas('fanclub')
            ->orderBy('item_name')
            ->get(['id', 'item_name']);

        return view('admin.fanclubs.contents.create', compact('casts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cast_id' => [
                'required',
                'integer',
                Rule::exists('items', 'id')->where(fn ($q) => $q->whereRaw(
                    'exists (select 1 from `fanclubs` where `fanclubs`.`cast_id` = `items`.`id`)'
                )),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_paid' => ['nullable', 'boolean'],
        ]);

        $cast = Item::query()->findOrFail($validated['cast_id']);

        $content = $this->contentService->create($cast, [
            'category_id' => null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_paid' => $request->boolean('is_paid'),
            'scheduled_at' => null,
            'next_release_at' => null,
            'published_at' => null,
        ]);

        return redirect()
            ->route('admin.fanclubs.contents.children.index', $content)
            ->with('success', 'チャンネル（投稿）を作成しました。ポストを追加してください。');
    }

    public function show(FanclubContent $fanclub_content)
    {
        $fanclub_content->load(['cast', 'category', 'children' => function ($q) {
            $q->orderBy('sort')->orderByDesc('id')->with('items');
        }]);

        if (request()->expectsJson()) {
            return response()->json($fanclub_content);
        }

        return view('admin.fanclubs.contents.show', ['content' => $fanclub_content]);
    }

    public function edit(FanclubContent $fanclub_content)
    {
        $categories = FanclubContentCategory::query()
            ->where('cast_id', $fanclub_content->cast_id)
            ->orderBy('sort')
            ->get();

        return view('admin.fanclubs.contents.edit', ['content' => $fanclub_content, 'categories' => $categories]);
    }

    public function update(Request $request, FanclubContent $fanclub_content)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:fanclub_contents_categories,id'],
            'is_paid' => ['nullable', 'boolean'],
            'scheduled_at' => ['nullable', 'date'],
            'next_release_at' => ['nullable', 'string', 'max:512'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['is_paid'] = $request->boolean('is_paid');
        $this->contentService->update($fanclub_content, $validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'updated']);
        }

        return redirect()->route('admin.fanclubs.contents.index')->with('success', 'チャンネル投稿を更新しました。');
    }

    public function destroy(FanclubContent $fanclub_content)
    {
        $fanclub_content->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'deleted']);
        }

        return redirect()->route('admin.fanclubs.contents.index')->with('success', '投稿を削除しました。');
    }
}
