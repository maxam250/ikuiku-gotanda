<?php

namespace App\Http\Controllers\Api\Fanclub;

use App\Http\Controllers\Controller;
use App\Http\Resources\FanclubContentResource;
use App\Models\FanclubContent;
use App\Models\FanclubContentCategory;
use App\Services\Fanclub\FanclubContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CastContentController extends Controller
{
    public function __construct(
        private readonly FanclubContentService $contentService
    ) {
    }

    public function index()
    {
        $cast = Auth::guard('item')->user();
        $contents = FanclubContent::query()
            ->with(['category', 'children' => fn ($q) => $q->orderBy('sort')->orderByDesc('id')->with('items')])
            ->withCount('children')
            ->where('cast_id', $cast->id)
            ->latest()
            ->paginate(20);

        return FanclubContentResource::collection($contents);
    }

    public function store(Request $request)
    {
        $cast = Auth::guard('item')->user();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:fanclub_contents_categories,id'],
            'new_category_name' => ['nullable', 'string', 'max:100'],
            'is_paid' => ['nullable', 'boolean'],
            'scheduled_at' => ['nullable', 'date'],
            'next_release_at' => ['nullable', 'string', 'max:512'],
            'published_at' => ['nullable', 'date'],
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
        $validated['is_paid'] = $request->boolean('is_paid');
        $content = $this->contentService->create($cast, $validated);

        return (new FanclubContentResource($content->load(['category', 'children'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, FanclubContent $fanclub_content)
    {
        $cast = Auth::guard('item')->user();
        abort_if((int) $fanclub_content->cast_id !== (int) $cast->id, 403);

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
        $fanclub_content = $this->contentService->update($fanclub_content, $validated);

        return new FanclubContentResource($fanclub_content->load(['category', 'children' => fn ($q) => $q->orderBy('sort')->with('items')]));
    }

    public function destroy(FanclubContent $fanclub_content)
    {
        $cast = Auth::guard('item')->user();
        abort_if((int) $fanclub_content->cast_id !== (int) $cast->id, 403);
        $fanclub_content->delete();

        return response()->json(['message' => 'deleted']);
    }
}
