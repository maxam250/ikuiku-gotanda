<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Shop;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Member;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::orderBy('item_name', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();
        $query = Gallery::with(['item', 'member', 'images' => fn($q) => $q->orderBy('sort')]);

        // 基本条件：公開設定が有効なもののみ
        $query->where('active', 1);

        if (Auth::guard('member')->check()) {
            // ログインユーザーの場合
            // flag = 0（全員）または flag = 1（ログインユーザーのみ）を表示
            $query->whereIn('flag', [0, 1]);
        } else {
            // 非ログインユーザーの場合
            // flag = 0（全員）のみ表示
            $query->where('flag', 0);
        }

        $galleries = $query->where('active', 1)
                    ->orderByDesc('id')
                    ->paginate(30);

        return view('galleries.index', compact('galleries', 'items', 'members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery)
    {
        // 公開されていないギャラリーは404
        if ($gallery->active != 1) {
            abort(404);
        }

        // メンバー専用制御
        if ($gallery->flag == 1 && !Auth::guard('member')->check()) {
            abort(403, 'このギャラリーは会員専用です');
        }

        // 限定公開（flag = 2）の場合はリンク直打ちで許可（任意で制限可能）

        $gallery->load([
            'images' => fn($q) => $q->orderBy('sort'),
            'item',
            'member'
        ]);


        $items = Item::orderBy('item_name', 'asc')->get();
        $members = Member::where('kind', '!=', '0')->orderBy('id', 'desc')->get();

        return view('galleries.show', compact('gallery', 'items', 'members'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gallery $gallery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gallery $gallery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        //
    }
}
