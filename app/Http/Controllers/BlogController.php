<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{    
    /**
     * 記事一覧ページを表示
     */
    public function index()
    {
        // 公開済み記事を作成日時の降順で取得（ページネーション付き）
        $blogs = Blog::where('is_published', true)->orderBy('created_at', 'desc')->paginate(12);

        return view('blogs.index', compact('blogs'));
    }

    /**
     * 記事詳細ページを表示
     */
    public function show($id)
    {
        $blogItem = Blog::findOrFail($id);

        return view('blogs.show', compact('blogItem'));
    }
}
