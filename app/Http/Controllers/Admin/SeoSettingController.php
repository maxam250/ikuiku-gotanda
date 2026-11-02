<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SeoSettingController extends Controller
{
    public function index()
    {
        $seoSettings = SeoSetting::orderBy('id')->get();
        return view('admin.seo-settings.index', compact('seoSettings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_key'  => 'required|string|max:100|unique:seo_settings,page_key',
            'page_name' => 'required|string|max:100',
        ]);

        SeoSetting::create([
            'page_key'  => $request->page_key,
            'page_name' => $request->page_name,
        ]);

        return redirect()->route('admin.seo-settings.index')
            ->with('success', '「' . $request->page_name . '」を新規登録しました');
    }

    public function edit(SeoSetting $seoSetting)
    {
        $allPages = SeoSetting::orderBy('page_name')->get();
        return view('admin.seo-settings.edit', compact('seoSetting', 'allPages'));
    }

    public function update(Request $request, SeoSetting $seoSetting)
    {
        $request->validate([
            'page_name'            => 'required|string|max:100',
            'meta_title'           => 'nullable|string|max:60',
            'meta_description'     => 'nullable|string|max:160',
            'meta_keywords'        => 'nullable|string|max:255',
            'og_title'             => 'nullable|string|max:60',
            'og_description'       => 'nullable|string|max:200',
            'og_image'             => 'nullable|string|max:500',
            'canonical_url'        => 'nullable|url|max:500',
            'breadcrumb_label'     => 'nullable|string|max:100',
            'breadcrumb_parent_key'=> 'nullable|string|max:100',
        ]);

        $seoSetting->update([
            'page_name'            => $request->page_name,
            'meta_title'           => $request->meta_title,
            'meta_description'     => $request->meta_description,
            'meta_keywords'        => $request->meta_keywords,
            'og_title'             => $request->og_title,
            'og_description'       => $request->og_description,
            'og_image'             => $request->og_image,
            'noindex'              => $request->boolean('noindex'),
            'canonical_url'        => $request->canonical_url,
            'breadcrumb_label'     => $request->breadcrumb_label,
            'breadcrumb_parent_key'=> $request->breadcrumb_parent_key ?: null,
        ]);

        return redirect()->route('admin.seo-settings.index')
            ->with('success', '「' . $seoSetting->page_name . '」のSEO設定を更新しました');
    }

    public function destroy(SeoSetting $seoSetting)
    {
        $name = $seoSetting->page_name;
        $seoSetting->delete();

        return redirect()->route('admin.seo-settings.index')
            ->with('success', '「' . $name . '」を削除しました');
    }
}
