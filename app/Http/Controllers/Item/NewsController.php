<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $news = News::orderBy('sort', 'asc')->orderBy('id', 'desc')->get();
        return view('item.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('item.news.create');
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
            $file = $request->file('filename');
            $filename = "news_" . time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする
            $file->move(public_path('uploads/news'), $filename); // 画像を保存
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

        return redirect()->route('item.news.index')->with('success', 'ニュースが作成されました');
    }

    // ニュースを表示 (GET /item/news/{news})
    public function show(News $news)
    {
        return view('item.news.show', compact('news'));
    }

    // ニュース編集フォームを表示 (GET /item/news/{news}/edit)
    public function edit(News $news)
    {
        return view('item.news.edit', compact('news'));
    }

    // ニュースを更新 (PUT /item/news/{news})
    public function update(Request $request, News $news)
    {
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
            if (file_exists(public_path('uploads/news/' . $news->filename))) {
                unlink(public_path('uploads/news/' . $news->filename)); // 画像ファイルの削除
            }
            $news->filename = null; // データベースのfilenameカラムをnullにする
        }

        // 画像の保存処理
        $filename = $news->filename; // 既存のファイル名を保持

        if ($request->hasFile('filename')) {
            // 古い画像が存在している場合は削除
            if ($filename && file_exists(public_path('uploads/news/' . $filename))) {
                unlink(public_path('uploads/news/' . $filename)); // 古い画像を削除
            }

            // 新しい画像の保存処理
            $file = $request->file('filename');
            $filename = 'news_' . time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする
            $file->move(public_path('uploads/news'), $filename); // 新しい画像を保存
        }

        // `timer_flag` の値を適切に設定

        $timer_flag = $request->has('timer_flag') ? 1 : 0;

        // ニュースの更新
        $news->update([
            'title' => $request->title,
            'body' => $request->body,
            'filename' => $filename,
            'timer_flag' => $timer_flag,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'active' => $request->active ? 1 : 0,
            'sort' => $request->sort ?? 0,
            'position' => $request->position ?? 0,
            'cate' => $request->cate ?? 0,
            'link' => $request->link,
        ]);

        return redirect()->route('item.news.index')->with('success', 'ニュースが更新されました');
    }


    // ニュースを削除 (DELETE /item/news/{news})
    public function destroy($news)
    {
        $news = News::findOrFail($news);

        // 画像が存在している場合は削除
        if ($news->filename && file_exists(public_path('uploads/news/' . $news->filename))) {
            unlink(public_path('uploads/news/' . $news->filename)); // 古い画像を削除
        }

        // ニュースデータの削除
        $news->delete();

        return redirect()->route('item.news.index')->with('success', 'ニュースが削除されました');
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
