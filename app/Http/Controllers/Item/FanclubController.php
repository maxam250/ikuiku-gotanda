<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Fanclub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FanclubController extends Controller
{
    public function edit()
    {
        $cast = Auth::guard('item')->user();
        $fanclub = Fanclub::firstOrCreate(
            ['cast_id' => $cast->id],
            [
                'title' => $cast->item_name . ' 公式ファンクラブ',
                'description' => null,
                'price' => 0,
                'status' => 'active',
            ]
        );

        Gate::authorize('update', $fanclub);

        return view('item.fanclubs.edit', compact('fanclub', 'cast'));
    }

    public function update(Request $request)
    {
        $cast = Auth::guard('item')->user();
        $fanclub = Fanclub::firstOrCreate(['cast_id' => $cast->id]);

        Gate::authorize('update', $fanclub);

        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'price'             => ['required', 'integer', 'min:0'],
            'status'            => ['required', 'in:active,paused,canceled'],
            'main_image_file'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'remove_main_image' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_main_image') && $fanclub->main_image_path) {
            Storage::disk('public')->delete($fanclub->main_image_path);
            $fanclub->main_image_path = null;
        }

        if ($request->hasFile('main_image_file')) {
            if ($fanclub->main_image_path) {
                Storage::disk('public')->delete($fanclub->main_image_path);
            }
            $fanclub->main_image_path = $request->file('main_image_file')->store('fanclub/main', 'public');
        }

        $fanclub->update(collect($validated)->only(['title', 'description', 'price', 'status'])->toArray());

        return redirect()
            ->route('item.fanclub.edit')
            ->with('success', 'ファンクラブ情報を更新しました。');
    }

    public function members()
    {
        $cast = Auth::guard('item')->user();
        $fanclub = Fanclub::where('cast_id', $cast->id)->firstOrFail();
        Gate::authorize('viewMembers', $fanclub);

        $memberships = $fanclub->memberships()
            ->with('member')
            ->latest()
            ->paginate(30);

        return view('item.fanclubs.members', compact('fanclub', 'memberships'));
    }
}

