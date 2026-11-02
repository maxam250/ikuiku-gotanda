<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageCompressor;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'main_image' => ['nullable', 'image', 'max:2048'],
            'sub_images.*' => 'nullable|image|max:2048',
            'is_published' => ['boolean'],
        ]);

        // メイン画像
        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('blogs', 'public');
            ImageCompressor::compressIfNeeded(storage_path('app/public/' . $path));
            $validated['main_image'] = $path;
        }

        // サブ画像
        $subImagePaths = [];
        if ($request->hasFile('sub_images')) {
            foreach ($request->file('sub_images') as $image) {
                $subPath = $image->store('blogs/sub', 'public');
                ImageCompressor::compressIfNeeded(storage_path('app/public/' . $subPath));
                $subImagePaths[] = $subPath;
            }
        }

        $validated['sub_images'] = $subImagePaths;

        $validated['is_published'] = $request->boolean('is_published', false);

        Blog::create($validated);

        return redirect()->route('admin.blogs.index')
            ->with('success', '投稿が作成されました。');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'main_image' => ['nullable', 'image', 'max:2048'],
            'sub_images.*' => 'nullable|image|max:2048',
            'is_published' => ['boolean'],
        ]);

        // メイン画像の更新
        if ($request->hasFile('main_image')) {
            if ($blog->main_image) {
                Storage::disk('public')->delete($blog->main_image);
            }
            $path = $request->file('main_image')->store('blogs', 'public');
            ImageCompressor::compressIfNeeded(storage_path('app/public/' . $path));
            $validated['main_image'] = $path;
        }

        // サブ画像の追加（既存を保持して追記）
        if ($request->hasFile('sub_images')) {
            $existingSubImages = $blog->sub_images ?? [];
            $newSubImages = [];
            foreach ($request->file('sub_images') as $image) {
                $subPath = $image->store('blogs/sub', 'public');
                ImageCompressor::compressIfNeeded(storage_path('app/public/' . $subPath));
                $newSubImages[] = $subPath;
            }
            $validated['sub_images'] = array_merge($existingSubImages, $newSubImages);
        }


        $validated['is_published'] = $request->boolean('is_published', false);

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')
            ->with('success', '投稿が更新されました。');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->main_image) {
            Storage::disk('public')->delete($blog->main_image);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', '投稿が削除されました。');
    }

    public function deleteMainImage(Blog $blog)
    {
        if ($blog->main_image) {
            Storage::disk('public')->delete($blog->main_image);
            $blog->main_image = null;
            $blog->save();
        }

        return back()->with('success', 'メイン画像を削除しました。');
    }

    public function deleteSubImage(Blog $blog, $index)
    {
        $images = $blog->sub_images ?? [];

        if (isset($images[$index])) {
            Storage::disk('public')->delete($images[$index]);
            unset($images[$index]);
            $blog->sub_images = array_values($images); // インデックス再構成
            $blog->save();
        }

        return back()->with('success', 'サブ画像を削除しました。');
    }
}
