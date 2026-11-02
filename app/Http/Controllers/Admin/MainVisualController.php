<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainVisual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MainVisualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mainVisuals = MainVisual::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.main-visuals.index', compact('mainVisuals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.main-visuals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pc_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'link_url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'title', 'description', 'link_url', 'sort_order'
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['show_on_pc'] = $request->has('show_on_pc');
        $data['show_on_mobile'] = $request->has('show_on_mobile');
        $data['start_date'] = $request->start_date;
        $data['end_date'] = $request->end_date;
        $data['sort_order'] = $request->sort_order ?? MainVisual::getNextSortOrder();

        // PC画像アップロード
        if ($request->hasFile('pc_image')) {
            $pcImage = $request->file('pc_image');
            $pcPath = $pcImage->store('main-visuals/pc', 'public');
            $data['pc_image_path'] = $pcPath;
        }

        // スマホ画像アップロード
        if ($request->hasFile('mobile_image')) {
            $mobileImage = $request->file('mobile_image');
            $mobilePath = $mobileImage->store('main-visuals/mobile', 'public');
            $data['mobile_image_path'] = $mobilePath;
        }

        MainVisual::create($data);

        return redirect()->route('admin.main-visuals.index')
            ->with('success', 'メインビジュアルを作成しました。');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MainVisual $mainVisual)
    {
        return view('admin.main-visuals.edit', compact('mainVisual'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MainVisual $mainVisual)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pc_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'link_url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'title', 'description', 'link_url', 'sort_order'
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['show_on_pc'] = $request->has('show_on_pc');
        $data['show_on_mobile'] = $request->has('show_on_mobile');
        $data['start_date'] = $request->start_date;
        $data['end_date'] = $request->end_date;

        // PC画像アップロード
        if ($request->hasFile('pc_image')) {
            // 古い画像を削除
            if ($mainVisual->pc_image_path) {
                Storage::disk('public')->delete($mainVisual->pc_image_path);
            }

            $pcImage = $request->file('pc_image');
            $pcPath = $pcImage->store('main-visuals/pc', 'public');
            $data['pc_image_path'] = $pcPath;
        }

        // スマホ画像アップロード
        if ($request->hasFile('mobile_image')) {
            // 古い画像を削除
            if ($mainVisual->mobile_image_path) {
                Storage::disk('public')->delete($mainVisual->mobile_image_path);
            }

            $mobileImage = $request->file('mobile_image');
            $mobilePath = $mobileImage->store('main-visuals/mobile', 'public');
            $data['mobile_image_path'] = $mobilePath;
        }

        $mainVisual->update($data);

        return redirect()->route('admin.main-visuals.index')
            ->with('success', 'メインビジュアルを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MainVisual $mainVisual)
    {
        // 画像ファイルを削除
        if ($mainVisual->pc_image_path) {
            Storage::disk('public')->delete($mainVisual->pc_image_path);
        }

        if ($mainVisual->mobile_image_path) {
            Storage::disk('public')->delete($mainVisual->mobile_image_path);
        }

        $mainVisual->delete();

        return redirect()->route('admin.main-visuals.index')
            ->with('success', 'メインビジュアルを削除しました。');
    }

    /**
     * 並び順を更新
     */
    public function sort(Request $request)
    {
        $items = $request->input('items', []);

        MainVisual::updateSortOrder($items);

        return response()->json(['success' => true]);
    }
}
