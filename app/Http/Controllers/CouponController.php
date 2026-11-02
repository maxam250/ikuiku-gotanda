<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * クーポン一覧を表示
     */
    public function index()
    {
        $coupons = Coupon::with(['items'])
            ->active()
            ->valid()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('coupons.index', compact('coupons'));
    }

    /**
     * クーポン詳細を表示
     */
    public function show($id)
    {
        $coupon = Coupon::with(['items'])
            ->active()
            ->valid()
            ->findOrFail($id);

        // 関連するクーポンを取得
        $relatedCoupons = Coupon::with(['items'])
            ->active()
            ->valid()
            ->where('id', '!=', $id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('coupons.show', compact('coupon', 'relatedCoupons'));
    }
}

