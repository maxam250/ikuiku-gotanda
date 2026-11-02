<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FanclubContent;
use App\Models\FanclubContentChild;
use App\Services\Fanclub\FanclubContentChildService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FanclubContentChildController extends Controller
{
    public function __construct(
        private readonly FanclubContentChildService $childService
    ) {
    }

    public function index(FanclubContent $fanclub_content)
    {
        $children = $fanclub_content->children()->with('items')->orderBy('sort')->orderByDesc('id')->get();

        if (request()->expectsJson()) {
            return response()->json($children);
        }

        return view('admin.fanclubs.content_children.index', [
            'content' => $fanclub_content,
            'children' => $children,
        ]);
    }

    public function create(FanclubContent $fanclub_content)
    {
        return view('admin.fanclubs.content_children.create', ['content' => $fanclub_content]);
    }

    public function store(Request $request, FanclubContent $fanclub_content)
    {
        $validated = $this->validateChild($request, false, null);

        $this->childService->create(
            $fanclub_content,
            $validated,
            $validated['items'],
            $request->file('items', []),
            $request->file('teaser_image_file')
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'created'], 201);
        }

        return redirect()
            ->route('admin.fanclubs.contents.children.index', $fanclub_content)
            ->with('success', 'ポストを作成しました。');
    }

    public function edit(FanclubContent $fanclub_content, FanclubContentChild $child)
    {
        Gate::authorize('manage', $child);
        abort_if((int) $child->fanclub_content_id !== (int) $fanclub_content->id, 404);
        $child->load('items');

        return view('admin.fanclubs.content_children.edit', [
            'content' => $fanclub_content,
            'child' => $child,
        ]);
    }

    public function update(Request $request, FanclubContent $fanclub_content, FanclubContentChild $child)
    {
        Gate::authorize('manage', $child);
        abort_if((int) $child->fanclub_content_id !== (int) $fanclub_content->id, 404);

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
            ->route('admin.fanclubs.contents.children.index', $fanclub_content)
            ->with('success', 'ポストを更新しました。');
    }

    public function destroy(Request $request, FanclubContent $fanclub_content, FanclubContentChild $child)
    {
        Gate::authorize('manage', $child);
        abort_if((int) $child->fanclub_content_id !== (int) $fanclub_content->id, 404);
        $child->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'deleted']);
        }

        return redirect()
            ->route('admin.fanclubs.contents.children.index', $fanclub_content)
            ->with('success', 'ポストを削除しました。');
    }

    private function validateChild(Request $request, bool $isUpdate, ?FanclubContentChild $existing): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'scheduled_at' => ['nullable', 'date'],
            'published_at' => ['nullable', 'date'],
            'teaser_enabled' => ['nullable', 'boolean'],
            'teaser_style' => ['nullable', Rule::in(['blur', 'custom_image'])],
            'teaser_reveal_at' => ['nullable', 'date'],
            'teaser_promo_note' => ['nullable', 'string', 'max:500'],
            'teaser_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_teaser_image' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:image,video,text,link,timer'],
            'items.*.value' => ['nullable', 'array'],
            'items.*.sort' => ['nullable', 'integer', 'min:0'],
            'items.*.image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'items.*.video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm,video/avi,video/x-ms-wmv', 'max:10240'],
        ];

        $validated = $request->validate($rules);
        $validated['teaser_enabled'] = $request->boolean('teaser_enabled');
        $validated['teaser_style'] = $validated['teaser_style'] ?? 'blur';

        if ($validated['teaser_enabled'] && empty($validated['teaser_reveal_at'])) {
            throw ValidationException::withMessages([
                'teaser_reveal_at' => 'ティザー利用時は本編公開（ぼかし解除）日時を入力してください。',
            ]);
        }

        $hasTeaserPath = $existing && $existing->teaser_image_path;
        if ($validated['teaser_enabled'] && $validated['teaser_style'] === 'custom_image'
            && !$request->hasFile('teaser_image_file') && !($isUpdate && $hasTeaserPath)) {
            throw ValidationException::withMessages([
                'teaser_image_file' => 'ティザー画像（差し替え用）をアップロードしてください。',
            ]);
        }

        return $validated;
    }
}

