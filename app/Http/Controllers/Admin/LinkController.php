<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Link;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $links = Link::orderBy('position', 'asc')->orderBy('sort', 'asc')->get();
        return view('admin.links.index', compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.links.create');
    }


    public function store(Request $request)
    {

        // dd($request);
        $request->validate([
            'title' => 'required|string|max:255',
            'fullcode' => 'nullable|string',
            'filename' => 'nullable|image|max:2048',
            'sort' => 'integer',
            'position' => 'nullable|integer',
            'link' => 'nullable|string',
            'active' => 'nullable|boolean', // これでチェックされたら1、されてなければ0
        ]);

        $validated['active'] = $request->has('active') ? 1 : 0;


        // 画像の保存処理

        $filename = null;
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = "links_" . time() . '_' . $file->getClientOriginalName();

            \Log::info('Store method - Attempting to save new file', [
                'new_filename' => $filename,
                'storage_path' => 'link/' . $filename
            ]);

            try {
                $file->storeAs('link', $filename);
                \Log::info('Store method - File saved successfully');
            } catch (\Exception $e) {
                \Log::error('Store method - File save failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }


        Link::create([
            'title' => $request->title,
            'fullcode' => $request->fullcode,
            'filename' => $filename,
            'active' => $request->active ? 1 : 0,
            'sort' => $request->sort ?? 0,
            'position' => $request->position ?? 0,
            'link' => $request->link,
        ]);

        return redirect()->route('admin.links.index')->with('success', 'リンクが作成されました');
    }

    // リンクを表示 (GET /admin/links/{links})
    public function show(Link $link)
    {
        return view('admin.links.show', compact('links'));
    }

    // リンク編集フォームを表示 (GET /admin/links/{links}/edit)
    public function edit(Link $link)
    {
        return view('admin.links.edit', compact('link'));
    }

    // リンクを更新 (PUT /admin/links/{links})
    public function update(Request $request, Link $link)
    {
        \Log::info('Update method called', [
            'request' => $request->all(),
            'link_id' => $link->id
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|url',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'integer',
        ]);

        // 画像削除のチェックがある場合
        if ($request->has('delete_image') && $link->filename) {
            if (Storage::exists('link/' . $link->filename)) {
                Storage::delete('link/' . $link->filename);
            }
            $link->filename = null;
        }

        // 画像の保存処理
        $filename = $link->filename;

        if ($request->hasFile('filename')) {
            \Log::info('File upload detected', [
                'original_name' => $request->file('filename')->getClientOriginalName(),
                'mime_type' => $request->file('filename')->getMimeType(),
                'size' => $request->file('filename')->getSize()
            ]);

            // 古い画像が存在している場合は削除
            if ($filename && Storage::exists('link/' . $filename)) {
                Storage::delete('link/' . $filename);
            }

            // 新しい画像の保存処理
            $file = $request->file('filename');
            $filename = 'links_' . time() . '_' . $file->getClientOriginalName();

            \Log::info('Attempting to save new file', [
                'new_filename' => $filename,
                'storage_path' => 'link/' . $filename
            ]);

            try {
                $file->storeAs('link', $filename);
                \Log::info('File saved successfully');
            } catch (\Exception $e) {
                \Log::error('File save failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $updateData = [
            'title'    => $request->title,
            'fullcode' => $request->fullcode,
            'link'     => $request->link,
            'filename' => $filename,
            'sort'     => $request->sort ?? 0,
            'position' => $request->position ?? 0,
            'active'   => $request->has('active') ? 1 : 0,
        ];

        \Log::info('Updating link with data', $updateData);

        $link->update($updateData);

        return redirect()->route('admin.links.index')->with('success', 'リンクが更新されました');
    }


    // リンクを削除 (DELETE /admin/links/{links})
    public function destroy(Link $link)
    {
        // 画像が存在している場合は削除
        if ($link->filename && Storage::exists('link/' . $link->filename)) {
            Storage::delete('link/' . $link->filename);
        }

        // リンクデータの削除
        $link->delete();

        return redirect()->route('admin.links.index')->with('success', 'リンクが削除されました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $links = Link::find($item['id']);
            $links->timestamps = false;  // updated_atとcreated_atを無効化
            $links->sort = $item['position'];
            $links->save();
        }

        return response()->json(['success' => true]);
    }
}
