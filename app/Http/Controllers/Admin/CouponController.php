<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ImageCompressor;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Coupon::with('items')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%'.$s.'%')
                    ->orWhere('code', 'like', '%'.$s.'%');
            });
        }

        if ($request->filled('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active === '1');
        }

        $coupons = $query->paginate(20)->appends($request->query());

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::where('item_active', 1)->get();
        return view('admin.coupons.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponRequest $request)
    {
        $data = $request->validated();
        
        // 画像アップロード処理
        if ($request->hasFile('main_image')) {
            $image = $request->file('main_image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('coupons', $filename, 'public');
            ImageCompressor::compressIfNeeded(storage_path('app/public/' . $path));
            $data['main_image'] = $path;
        }

        // クーポンコードが空の場合は自動生成
        if (empty($data['code'])) {
            $data['code'] = 'CPN' . strtoupper(Str::random(8));
        }

        $coupon = Coupon::create($data);

        // アイテムとの関連付け
        if ($request->has('items')) {
            $items = $request->input('items', []);
            $couponData = $request->input('coupon_data', []);
            
            foreach ($items as $itemId) {
                $coupon->items()->attach($itemId, [
                    'coupon_data' => json_encode($couponData[$itemId] ?? [])
                ]);
            }
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'クーポンが正常に作成されました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load('items');
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $items = Item::where('item_active', 1)->get();
        $coupon->load('items');
        
        // 関連付けられたアイテムのIDを取得
        $selectedItems = $coupon->items->pluck('id')->toArray();
        
        return view('admin.coupons.edit', compact('coupon', 'items', 'selectedItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponRequest $request, Coupon $coupon)
    {
        $data = $request->validated();
        
        // 画像アップロード処理
        if ($request->hasFile('main_image')) {
            // 古い画像を削除
            if ($coupon->main_image) {
                Storage::disk('public')->delete($coupon->main_image);
            }

            $image = $request->file('main_image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('coupons', $filename, 'public');
            ImageCompressor::compressIfNeeded(storage_path('app/public/' . $path));
            $data['main_image'] = $path;
        }

        $coupon->update($data);

        // アイテムとの関連付けを更新
        $coupon->items()->detach();
        
        if ($request->has('items')) {
            $items = $request->input('items', []);
            $couponData = $request->input('coupon_data', []);
            
            foreach ($items as $itemId) {
                $coupon->items()->attach($itemId, [
                    'coupon_data' => json_encode($couponData[$itemId] ?? [])
                ]);
            }
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'クーポンが正常に更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        // 画像を削除
        if ($coupon->main_image) {
            Storage::disk('public')->delete($coupon->main_image);
        }

        // 関連付けを削除
        $coupon->items()->detach();
        
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'クーポンが正常に削除されました。');
    }

    /**
     * クーポンのステータスを切り替え
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        
        $status = $coupon->is_active ? '有効' : '無効';
        return redirect()->back()
            ->with('success', "クーポンが{$status}になりました。");
    }
}
