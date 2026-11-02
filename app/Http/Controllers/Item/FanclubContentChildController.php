<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Fanclub;
use App\Models\FanclubContent;
use App\Models\FanclubContentChild;
use App\Services\Fanclub\FanclubContentChildService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FanclubContentChildController extends Controller
{
    public function __construct(
        private readonly FanclubContentChildService $childService
    ) {
    }

    public function index(FanclubContent $content)
    {
        $cast = Auth::guard('item')->user();
        Gate::authorize('manage', $content);
        abort_if((int) $content->cast_id !== (int) $cast->id, 403);

        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();
        $children = $content->children()->with('items')->orderBy('sort')->orderByDesc('id')->get();

        if (request()->expectsJson()) {
            return response()->json($children);
        }

        return view('item.fanclub_content_children.index', compact('fanclub', 'content', 'children'));
    }

    public function create(FanclubContent $content)
    {
        $cast = Auth::guard('item')->user();
        Gate::authorize('manage', $content);
        abort_if((int) $content->cast_id !== (int) $cast->id, 403);

        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();

        return view('item.fanclub_content_children.create', compact('fanclub', 'content'));
    }

    public function store(Request $request, FanclubContent $content)
    {
        $cast = Auth::guard('item')->user();
        Gate::authorize('manage', $content);
        abort_if((int) $content->cast_id !== (int) $cast->id, 403);

        $validated = $this->validateChild($request, false, null);

        $child = $this->childService->create(
            $content,
            $validated,
            $validated['items'],
            $request->file('items', []),
            $request->file('teaser_image_file')
        );

        if ($request->expectsJson()) {
            return response()->json(['id' => $child->id], 201);
        }

        return redirect()
            ->route('item.fanclub.contents.children.index', $content)
            ->with('success', 'ポストを作成しました。');
    }

    public function edit(FanclubContent $content, FanclubContentChild $child)
    {
        $cast = Auth::guard('item')->user();
        Gate::authorize('manage', $child);
        abort_if((int) $child->fanclub_content_id !== (int) $content->id, 404);
        abort_if((int) $content->cast_id !== (int) $cast->id, 403);

        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();
        $child->load('items');

        return view('item.fanclub_content_children.edit', compact('fanclub', 'content', 'child'));
    }

    public function update(Request $request, FanclubContent $content, FanclubContentChild $child)
    {
        Gate::authorize('manage', $child);
        abort_if((int) $child->fanclub_content_id !== (int) $content->id, 404);

        $validated = $this->validateChild($request, true, $child);
        $removeTeaser = $request->boolean('remove_teaser_image');

        $this->childService->update(
            $child,
            $validated,
            $validated['items'],
            $request->file('items', []),
            $request->file('teaser_image_file'),
            $removeTeaser
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'updated']);
        }

        return redirect()
            ->route('item.fanclub.contents.children.index', $content)
            ->with('success', 'ポストを更新しました。');
    }

    public function destroy(Request $request, FanclubContent $content, FanclubContentChild $child)
    {
        Gate::authorize('manage', $child);
        abort_if((int) $child->fanclub_content_id !== (int) $content->id, 404);
        $this->childService->deleteWithFiles($child);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'deleted']);
        }

        return redirect()
            ->route('item.fanclub.contents.children.index', $content)
            ->with('success', 'ポストを削除しました。');
    }

    public function publishNow(Request $request, FanclubContent $content, FanclubContentChild $child)
    {
        Gate::authorize('manage', $child);
        abort_if((int) $child->fanclub_content_id !== (int) $content->id, 404);
        $child->update([
            'published_at' => now(),
            'scheduled_at' => null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'published']);
        }

        return redirect()
            ->route('item.fanclub.contents.children.index', $content)
            ->with('success', 'ポストを即時公開しました。');
    }

    private function validateChild(Request $request, bool $isUpdate, ?FanclubContentChild $existing): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'publish_status' => ['nullable', 'in:public,private'],
            'scheduled_at' => ['nullable', 'date'],
            'teaser_enabled' => ['nullable', 'boolean'],
            'teaser_style' => ['nullable', Rule::in(['blur', 'custom_image'])],
            'teaser_promo_note' => ['nullable', 'string', 'max:500'],
            'teaser_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_teaser_image' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:image,video,text,link,timer'],
            'items.*.value' => ['nullable', 'array'],
            'items.*.sort' => ['nullable', 'integer', 'min:0'],
            'items.*.image_file'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'items.*.video_file'   => ['nullable', 'file', 'max:102400'],
            'items.*.remove_image' => ['nullable', 'boolean'],
            'items.*.remove_video' => ['nullable', 'boolean'],
        ];

        $validated = $request->validate($rules);
        $validated['teaser_enabled'] = $request->boolean('teaser_enabled');
        $validated['teaser_style'] = $validated['teaser_style'] ?? 'blur';

        $teaserEnabled = $validated['teaser_enabled'];
        $publishStatus = $validated['publish_status'] ?? 'private';
        $scheduledAt = $validated['scheduled_at'] ?? null;

        // scheduled_at の役割: ティーザーOFF→予約投稿(DB scheduled_at), ティーザーON→解除日時(DB teaser_reveal_at)
        if ($teaserEnabled) {
            $validated['teaser_reveal_at'] = $scheduledAt;
            $validated['scheduled_at'] = null;
        } else {
            $validated['teaser_reveal_at'] = null;
        }

        // published_at を publish_status と scheduled_at から決定
        if (!$teaserEnabled && $scheduledAt) {
            $validated['published_at'] = null;
        } elseif ($publishStatus === 'public') {
            $validated['published_at'] = $existing?->published_at ?? now();
        } else {
            $validated['published_at'] = null;
        }

        $hasTeaserPath = $existing && $existing->teaser_image_path;
        if ($teaserEnabled && $validated['teaser_style'] === 'custom_image'
            && !$request->hasFile('teaser_image_file') && !($isUpdate && $hasTeaserPath)) {
            throw ValidationException::withMessages([
                'teaser_image_file' => 'ティーザー画像（差し替え用）をアップロードしてください。',
            ]);
        }

        return $validated;
    }
}

