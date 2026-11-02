<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    /**
     * ニュース一覧ページを表示
     */
    public function index()
    {
        $news = News::publiclyVisible()
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(12);

        return view('news.index', compact('news'));
    }

    /**
     * ニュース詳細ページを表示
     */
    public function show($id)
    {
        $newsItem = News::publiclyVisible()->findOrFail($id);

        return view('news.show', compact('newsItem'));
    }
}
