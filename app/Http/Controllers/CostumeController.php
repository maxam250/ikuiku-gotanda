<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Costume;
use App\Models\CostumeCate;

class CostumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // アクティブなカテゴリを取得
        $categories = CostumeCate::where('active', 1)
            ->orderBy('sort', 'asc')
            ->get();

        // 選択されたカテゴリID（デフォルトは最初のカテゴリまたは全て）
        $selectedCategory = $request->get('category');

        // コスチュームのクエリを構築
        $query = Costume::where('active', 1);

        // カテゴリでフィルタリング
        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('cate', $selectedCategory);
        }

        $costumes = $query->with('costume_cate')
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('costume', compact('costumes', 'categories', 'selectedCategory'));
    }
}
