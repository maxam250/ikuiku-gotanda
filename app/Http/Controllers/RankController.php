<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rank;
use App\Models\RankCate;
use App\Models\Item;
use App\Models\ItemRank;
use App\Models\Fanclub;
use App\Models\FanclubMembership;
use App\Models\FanclubContent;

class RankController extends Controller
{
    public function index()
    {
        $ranks     = Rank::with(['itemRanks.items'])->where('active', '1')->orderBy('sort', 'asc')->get();
        $rankCates = RankCate::orderBy('sort', 'asc')->get();

        // ファンクラブ登録数ランキング（active会員数 上位10）
        // $fcMemberRanking = FanclubMembership::selectRaw('fanclub_id, COUNT(*) as member_count')
        //     ->where('status', 'active')
        //     ->groupBy('fanclub_id')
        //     ->orderByDesc('member_count')
        //     ->limit(10)
        //     ->with('fanclub.cast.thumbnail')
        //     ->get()
        //     ->filter(fn($m) => $m->fanclub && $m->fanclub->cast);

        // // ファンクラブ配信ランキング（公開済みコンテンツ数 上位10）
        // $fcContentRanking = FanclubContent::selectRaw('cast_id, COUNT(*) as post_count')
        //     ->whereNotNull('published_at')
        //     ->where('published_at', '<=', now())
        //     ->groupBy('cast_id')
        //     ->orderByDesc('post_count')
        //     ->limit(10)
        //     ->with('cast.thumbnail')
        //     ->get()
        //     ->filter(fn($c) => $c->cast);

        // return view('rank', compact('ranks', 'rankCates', 'fcMemberRanking', 'fcContentRanking'));
        return view('rank', compact('ranks', 'rankCates'));
    }
}
