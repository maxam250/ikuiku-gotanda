<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\SeoSetting;
use App\Models\Shop;
use App\Models\ShopGroup;
use Illuminate\Http\Request;

class AgeVerificationController extends Controller
{
    public function index()
    {
        $shop    = Shop::first();
        $seoData = SeoSetting::findByRouteKey('age_verification');
        $links   = Link::where('active', 1)
            ->orderBy('position', 'asc')
            ->orderBy('sort', 'asc')
            ->get();
        $shopGroups = ShopGroup::where('is_publish', 1)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('age_verification', compact('shop', 'seoData', 'links', 'shopGroups'));
    }

    public function verify(Request $request)
    {
        if ($request->input('agree') === 'yes') {
            return redirect()->route('top')->cookie('age_verified', '1', 60 * 24 * 30);
        }
        return redirect()->back();
    }
}
