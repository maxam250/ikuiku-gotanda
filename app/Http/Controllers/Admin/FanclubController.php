<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fanclub;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageCompressor;

class FanclubController extends Controller
{
    public function index()
    {
        $fanclubs = Fanclub::query()
            ->with('cast')
            ->withCount([
                'memberships',
                'memberships as active_memberships_count' => fn ($q) => $q->where('status', 'active'),
            ])
            ->withCount('contents')
            ->latest()
            ->paginate(30);

        return view('admin.fanclubs.index', compact('fanclubs'));
    }

    public function show(Fanclub $fanclub)
    {
        $fanclub->load([
            'cast',
            'memberships.member',
            'contents' => fn ($q) => $q->withCount('children')->latest()->take(10),
        ]);

        $activeCount = $fanclub->memberships->where('status', 'active')->count();

        return view('admin.fanclubs.show', compact('fanclub', 'activeCount'));
    }

    public function create()
    {
        // ファンクラブ未登録のキャストのみ選択肢に表示
        $existingCastIds = Fanclub::pluck('cast_id');
        $casts = Item::whereNotIn('id', $existingCastIds)
            ->where('item_active', 1)
            ->orderBy('item_sort')
            ->get();

        return view('admin.fanclubs.create', compact('casts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cast_id'     => ['required', 'integer', 'exists:items,id', 'unique:fanclubs,cast_id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'integer', 'min:0'],
            'status'      => ['required', 'in:active,paused,canceled'],
        ]);

        Fanclub::create($validated);

        return redirect()->route('admin.fanclubs.index')->with('success', 'ファンクラブを作成しました。');
    }

    public function edit(Fanclub $fanclub)
    {
        $fanclub->load('cast');

        return view('admin.fanclubs.edit', compact('fanclub'));
    }

    public function update(Request $request, Fanclub $fanclub)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'price'            => ['required', 'integer', 'min:0'],
            'status'           => ['required', 'in:active,paused,canceled'],
            'main_image_file'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'remove_main_image'=> ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_main_image') && $fanclub->main_image_path) {
            Storage::disk('public')->delete($fanclub->main_image_path);
            $fanclub->main_image_path = null;
        }

        if ($request->hasFile('main_image_file')) {
            if ($fanclub->main_image_path) {
                Storage::disk('public')->delete($fanclub->main_image_path);
            }
            $storedPath = $request->file('main_image_file')->store('fanclub/main', 'public');
            ImageCompressor::compressIfNeeded(storage_path('app/public/' . $storedPath));
            $fanclub->main_image_path = $storedPath;
        }

        $fanclub->update(collect($validated)->only(['title', 'description', 'price', 'status'])->toArray());

        return redirect()->route('admin.fanclubs.index')->with('success', 'ファンクラブを更新しました。');
    }

    public function destroy(Fanclub $fanclub)
    {
        $fanclub->delete();

        return redirect()->route('admin.fanclubs.index')->with('success', 'ファンクラブを削除しました。');
    }
}
