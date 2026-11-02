<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use App\Models\RankCate;

class RankController extends Controller
{
    /**
     * 旧 ranking.php と同じ動き
     *   /rank           … 最新のランキング 1 件（sort 昇順・登録日の新しい順の先頭）
     *   /rank/{cate_id} … そのカテゴリのランキングを新しい順にすべて表示
     */
    public function index($cate_id = null)
    {
        $activeRanks = Rank::where('active', '1');

        // カテゴリボタン：旧サイトと同じく全カテゴリを id 順に表示
        $rankCates = RankCate::where('active', 1)
            ->orderBy('id', 'asc')
            ->get();

        $currentCate = null;
        if ($cate_id !== null && ctype_digit((string) $cate_id)) {
            $currentCate = RankCate::find($cate_id);
        }

        if ($currentCate) {
            $ranks = (clone $activeRanks)->where('cate_id', $currentCate->id)
                ->orderBy('id', 'desc')
                ->with(['itemRanks.items.thumbnail'])
                ->get();
        } else {
            $ranks = (clone $activeRanks)
                ->orderBy('sort', 'asc')
                ->orderBy('created_at', 'desc')
                ->with(['itemRanks.items.thumbnail'])
                ->limit(1)
                ->get();
        }

        return view('rank', compact('ranks', 'rankCates', 'currentCate'));
    }
}
