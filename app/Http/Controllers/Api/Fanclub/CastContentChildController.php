<?php

namespace App\Http\Controllers\Api\Fanclub;

use App\Http\Controllers\Controller;
use App\Http\Resources\FanclubContentChildResource;
use App\Models\FanclubContent;
use App\Models\FanclubContentChild;
use App\Services\Fanclub\FanclubContentChildService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CastContentChildController extends Controller
{
    public function __construct(
        private readonly FanclubContentChildService $childService
    ) {
    }

    public function index(FanclubContent $fanclub_content)
    {
        $cast = Auth::guard('item')->user();
        abort_if((int) $fanclub_content->cast_id !== (int) $cast->id, 403);

        $children = $fanclub_content->children()
            ->with('items')
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return FanclubContentChildResource::collection($children);
    }

    public function store(Request $request, FanclubContent $fanclub_content)
    {
        $cast = Auth::guard('item')->user();
        abort_if((int) $fanclub_content->cast_id !== (int) $cast->id, 403);

        $validated = $this->validated($request, false, null);
        $child = $this->childService->create(
            $fanclub_content,
            $validated,
            $validated['items'],
            $request->file('items', []),
            $request->file('teaser_image_file')
        );

        return (new FanclubContentChildResource($child->load('items')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, FanclubContent $fanclub_content, FanclubContentChild $child)
    {
        $cast = Auth::guard('item')->user();
        abort_if((int) $fanclub_content->cast_id !== (int) $cast->id, 403);
        abort_if((int) $child->fanclub_content_id !== (int) $fanclub_content->id, 404);

        $validated = $this->validated($request, true, $child);
        $child = $this->childService->update(
            $child,
            $validated,
            $validated['items'],
            $request->file('items', []),
            $request->file('teaser_image_file'),
            $request->boolean('remove_teaser_image')
        );

        return new FanclubContentChildResource($child->load('items'));
    }

    public function destroy(FanclubContent $fanclub_content, FanclubContentChild $child)
    {
        $cast = Auth::guard('item')->user();
        abort_if((int) $fanclub_content->cast_id !== (int) $cast->id, 403);
        abort_if((int) $child->fanclub_content_id !== (int) $fanclub_content->id, 404);
        $child->delete();

        return response()->json(['message' => 'deleted']);
    }

    private function validated(Request $request, bool $isUpdate, ?FanclubContentChild $existing): array
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
                'teaser_image_file' => 'ティザー画像をアップロードしてください。',
            ]);
        }

        return $validated;
    }
}

