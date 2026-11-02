<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Fanclub;
use App\Models\FanclubContent;
use App\Models\FanclubContentCategory;
use App\Services\Fanclub\FanclubContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FanclubContentController extends Controller
{
    public function __construct(
        private readonly FanclubContentService $contentService
    ) {
    }

    public function index()
    {
        $cast = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();
        Gate::authorize('update', $fanclub);

        $contents = FanclubContent::query()
            ->where('cast_id', $cast->id)
            ->with(['category'])
            ->withCount('children')
            ->latest()
            ->paginate(20);

        if (request()->expectsJson()) {
            return response()->json($contents);
        }

        return view('item.fanclub_contents.index', compact('fanclub', 'contents'));
    }

    public function create()
    {
        $cast = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();
        Gate::authorize('update', $fanclub);

        $categories = FanclubContentCategory::where('cast_id', $cast->id)->orderBy('sort')->get();

        return view('item.fanclub_contents.create', compact('fanclub', 'categories'));
    }

    public function store(Request $request)
    {
        $cast = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();
        Gate::authorize('update', $fanclub);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:fanclub_contents_categories,id'],
            'new_category_name' => ['nullable', 'string', 'max:100'],
            'publish_status' => ['nullable', 'in:public,private'],
            'scheduled_at' => ['nullable', 'date'],
            'next_release_at' => ['nullable', 'string', 'max:512'],
        ]);

        $categoryId = $validated['category_id'] ?? null;
        if (!empty($validated['new_category_name'])) {
            $category = FanclubContentCategory::create([
                'cast_id' => $cast->id,
                'name' => $validated['new_category_name'],
                'sort' => 0,
            ]);
            $categoryId = $category->id;
        }

        $validated['category_id'] = $categoryId;

        if (!empty($validated['scheduled_at'])) {
            $validated['published_at'] = null;
        } elseif (($validated['publish_status'] ?? 'private') === 'public') {
            $validated['published_at'] = now();
        } else {
            $validated['published_at'] = null;
        }

        $content = $this->contentService->create($cast, $validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'created', 'id' => $content->id], 201);
        }

        return redirect()
            ->route('item.fanclub.contents.children.index', $content)
            ->with('success', 'チャンネルを作成しました。ポストを追加してください。');
    }

    public function edit(FanclubContent $content)
    {
        $cast = Auth::guard('item')->user();
        Gate::authorize('manage', $content);

        if ((int) $content->cast_id !== (int) $cast->id) {
            abort(403);
        }

        $categories = FanclubContentCategory::where('cast_id', $cast->id)->orderBy('sort')->get();

        return view('item.fanclub_contents.edit', compact('content', 'categories'));
    }

    public function update(Request $request, FanclubContent $content)
    {
        Gate::authorize('manage', $content);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:fanclub_contents_categories,id'],
            'publish_status' => ['nullable', 'in:public,private'],
            'scheduled_at' => ['nullable', 'date'],
            'next_release_at' => ['nullable', 'string', 'max:512'],
        ]);

        if (!empty($validated['scheduled_at'])) {
            $validated['published_at'] = null;
        } elseif (($validated['publish_status'] ?? 'private') === 'public') {
            $validated['published_at'] = $content->published_at ?? now();
        } else {
            $validated['published_at'] = null;
        }

        $this->contentService->update($content, $validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'updated']);
        }

        return redirect()->route('item.fanclub.contents.index')
            ->with('success', 'チャンネルを更新しました。');
    }

    public function destroy(FanclubContent $content)
    {
        Gate::authorize('manage', $content);
        $content->delete();

        return redirect()
            ->route('item.fanclub.contents.index')
            ->with('success', '投稿（チャンネル・ポスト含む）を削除しました。');
    }

    public function publishNow(FanclubContent $content)
    {
        Gate::authorize('manage', $content);
        $content->update([
            'published_at' => now(),
            'scheduled_at' => null,
        ]);

        return redirect()
            ->route('item.fanclub.contents.index')
            ->with('success', 'チャンネルを即時公開しました。');
    }
}
