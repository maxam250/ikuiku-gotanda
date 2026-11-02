<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageCompressor;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $news = News::orderBy('sort', 'asc')->orderBy('id', 'desc')->get();
        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.news.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'filename' => 'nullable|image|max:2048',
            'sort' => 'integer',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
            'timer_flag' => 'nullable|boolean',
        ]);

        // `timer_flag` を設定
        $validated['timer_flag'] = $request->has('timer_flag') ? 1 : 0;

        // 画像の保存処理
        $filename = null;
        if ($request->hasFile('filename')) {
            \Log::info('Store method - File upload detected', [
                'original_name' => $request->file('filename')->getClientOriginalName(),
                'mime_type' => $request->file('filename')->getMimeType(),
                'size' => $request->file('filename')->getSize()
            ]);

            $file = $request->file('filename');
            $filename = "news_" . time() . '_' . $file->getClientOriginalName();

            \Log::info('Store method - Attempting to save new file', [
                'new_filename' => $filename,
                'storage_path' => 'news/' . $filename
            ]);

            try {
                $file->storeAs('news', $filename);
                ImageCompressor::compressIfNeeded(storage_path('app/news/' . $filename));
                \Log::info('Store method - File saved successfully');
            } catch (\Exception $e) {
                \Log::error('Store method - File save failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // 画像のファイル名を `$validated` に追加
        $validated['filename'] = $filename;

        // 他のデータの設定
        $validated['active'] = $request->active ? 1 : 0;
        $validated['sort'] = $request->sort ?? 0;
        $validated['position'] = $request->position ?? 0;
        $validated['cate'] = $request->cate ?? 0;
        $validated['link'] = $request->link;

        // データベースに保存
        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'ニュースが作成されました');
    }

    // ニュースを表示 (GET /admin/news/{news})
    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    // ニュース編集フォームを表示 (GET /admin/news/{news}/edit)
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    // ニュースを更新 (PUT /admin/news/{news})
    public function update(Request $request, News $news)
    {
        \Log::info('Update method called', [
            'request' => $request->all(),
            'news_id' => $news->id
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'integer',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
            'timer_flag' => 'nullable|boolean',
        ]);

        // 画像削除のチェックがある場合
        if ($request->has('delete_image') && $news->filename) {
            if (Storage::exists('news/' . $news->filename)) {
                Storage::delete('news/' . $news->filename);
            }
            $news->filename = null;
        }

        // 画像の保存処理
        $filename = $news->filename;

        if ($request->hasFile('filename')) {
            \Log::info('File upload detected', [
                'original_name' => $request->file('filename')->getClientOriginalName(),
                'mime_type' => $request->file('filename')->getMimeType(),
                'size' => $request->file('filename')->getSize()
            ]);

            // 古い画像が存在している場合は削除
            if ($filename && Storage::exists('news/' . $filename)) {
                Storage::delete('news/' . $filename);
            }

            // 新しい画像の保存処理
            $file = $request->file('filename');
            $filename = 'news_' . time() . '_' . $file->getClientOriginalName();

            \Log::info('Attempting to save new file', [
                'new_filename' => $filename,
                'storage_path' => 'news/' . $filename
            ]);

            try {
                $file->storeAs('news', $filename);
                ImageCompressor::compressIfNeeded(storage_path('app/news/' . $filename));
                \Log::info('File saved successfully');
            } catch (\Exception $e) {
                \Log::error('File save failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $updateData = [
            'title' => $request->title,
            'body' => $request->body,
            'filename' => $filename,
            'timer_flag' => $request->has('timer_flag') ? 1 : 0,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'active' => $request->active ? 1 : 0,
            'sort' => $request->sort ?? 0,
            'position' => $request->position ?? 0,
            'cate' => $request->cate ?? 0,
            'link' => $request->link,
        ];

        \Log::info('Updating news with data', $updateData);

        $news->update($updateData);

        return redirect()->route('admin.news.index')->with('success', 'ニュースが更新されました');
    }


    // ニュースを削除 (DELETE /admin/news/{news})
    public function destroy($news)
    {
        $news = News::findOrFail($news);

        // 画像が存在している場合は削除
        if ($news->filename && Storage::exists('news/' . $news->filename)) {
            Storage::delete('news/' . $news->filename);
        }

        // ニュースデータの削除
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'ニュースが削除されました');
    }



    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // ニュースを並び替え順序に基づいて更新
            // News::where('id', $item['id'])->update(['sort' => $item['position']]);

            // timestampsの自動更新を無効化して、並び替えを保存
            $news = News::find($item['id']);
            $news->timestamps = false;  // updated_atとcreated_atを無効化
            $news->sort = $item['position'];
            $news->save();
        }

        return response()->json(['success' => true]);
    }

}
