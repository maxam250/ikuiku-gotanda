<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Coupon;
use App\Models\Fanclub;
use App\Models\Gallery;
use App\Models\Item;
use App\Models\News;
use App\Models\SpecialEvent;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $items = Item::where('item_active', 1)
            ->select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $news = News::publiclyVisible()
            ->select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $blogs = Blog::where('is_published', true)
            ->select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $coupons = Coupon::active()->valid()
            ->select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $specialEvents = SpecialEvent::active()
            ->select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $fanclubs = Fanclub::where('status', 'active')
            ->select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $galleries = Gallery::where('active', 1)
            ->where('flag', 0)
            ->select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $content = view('sitemap', compact(
            'items', 'news', 'blogs', 'coupons', 'specialEvents', 'fanclubs', 'galleries'
        ))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
