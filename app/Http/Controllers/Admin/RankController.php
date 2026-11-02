<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\RankCate;
use App\Models\Item;
use App\Models\ItemRank;
use Illuminate\Http\Request;

class RankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // $ranks = Rank::orderBy('sort', 'asc')->get();

        // $rankCates = RankCate::orderBy('sort', 'asc')->get();
        // $itemRanks = ItemRank::orderBy('no', 'asc')->get();
        // $items = Items::orderBy('item_name', 'asc')->get();


        $rankCates = RankCate::orderBy('sort', 'asc')->get()->keyBy('id');
        $ranks = Rank::with(['itemRanks' => fn($q) => $q->orderBy('no', 'asc'), 'itemRanks.items.thumbnail'])
            ->orderBy('sort', 'asc')
            ->get()
            ->groupBy('cate_id');

        return view('admin.ranks.index', compact('ranks', 'rankCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $rankCates = RankCate::orderBy('sort', 'asc')->get();

        return view('admin.ranks.create', compact('rankCates'));
    }

    public function store(Request $request)
    {

        // バリデーション
        $request->validate([
            'active' => 'required|in:0,1',
            'title' => 'required|string|max:255',
            'cate_id' => 'nullable|integer|exists:rank_cates,id',
            'num' => 'required|integer|min:1|max:10',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
            'filename' => 'nullable|string|max:255',
        ]);



        // Rank::create($request->all());


        // ランキングの作成
        $rank = new Rank();
        $rank->active = $request->input('active');
        $rank->title = $request->input('title');
        $rank->cate_id = $request->input('cate_id');
        $rank->body = $request->input('body');
        $rank->position = $request->input('position');
        $rank->term_start = $request->input('term_start');
        $rank->term_end = $request->input('term_end');
        $rank->filename = $request->input('filename');
        $rank->save(); // 保存して、rank.idを取得

        // numで指定されたランキング数だけitem_rankテーブルに登録
        $num = $request->input('num');

        for ($i = 1; $i <= $num; $i++) {
            $itemRank = new ItemRank();
            $itemRank->rank_id = $rank->id;  // 先ほど保存したrank.idを使用
            $itemRank->no = $i;  // 1位、2位、3位…というnoを登録
            $itemRank->text = null;  // 初期状態はnull
            $itemRank->save();
        }


        return redirect()->route('admin.ranks.index')->with('success', 'ランキング追加完了');
    }

    public function edit($id)
    {
        $rank = Rank::findOrFail($id);
        $rankCates = RankCate::orderBy('sort', 'asc')->get();
        $itemRanks = ItemRank::where('rank_id', '=', $id)->orderBy('no', 'asc')->get();
        $items = Item::selectable($itemRanks->pluck('item_id'))->orderBy('item_name', 'asc')->get();

        return view('admin.ranks.edit', compact('rank', 'rankCates', 'itemRanks', 'items'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cate_id' => 'required',
            'title' => 'required',
        ]);

        $rank = Rank::findOrFail($id);
        $rank->update($request->all());

        return redirect()->route('admin.ranks.edit', ['rank' => $id])->with('success', 'ランキング更新完了');
    }


    public function destroy($id)
    {
        Rank::findOrFail($id)->delete();
        return redirect()->route('admin.ranks.index')->with('success', 'ランキング削除完了');
    }


    /**
     * 並び替え
     */
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $ranks = Rank::find($item['id']);
            $ranks->timestamps = false;  // updated_atとcreated_atを無効化
            $ranks->sort = $item['position'];
            $ranks->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * 各ランキングのキャストの設定
     * @param request
     * @param int rank_id
     * @return view
     */
    public function updateItemRank(Request $request, $rank_id)
    {
        // フォームから送信されたデータを取得
        $itemRanks = $request->input('item_rank'); // 配列で受け取る
        $textRanks = $request->input('text'); // 配列で受け取る


        foreach ($itemRanks as $no => $item_id) {
            // item_id が空の場合は null を設定
            $item_id = !empty($item_id) ? $item_id : null;
            $text = !empty($textRanks[$no]) ? $textRanks[$no] : null;


            // すでに存在する場合は更新、存在しない場合は作成
            ItemRank::updateOrCreate(
                [
                    'rank_id' => $rank_id,
                    'no' => $no,
                ],
                [
                    'item_id' => $item_id, // item_id を更新
                    'text' => $text,
                ]
            );
        }

        return redirect()->route('admin.ranks.edit', ['rank' => $rank_id])->with('success', 'ランキングのアイテムが更新されました');
    }

}
