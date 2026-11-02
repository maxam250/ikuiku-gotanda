<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Newface;
use Carbon\Carbon;

class NewfaceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $validItemIds = Item::pluck('id')->toArray();

        $newfaces = Newface::with(['item', 'itemImage'])
            ->whereIn('item_id', $validItemIds)
            ->orderBy('sort', 'asc')
            ->get();

        $registeredItemIds = $newfaces->pluck('item_id')->toArray();

        $non_newfaces = Item::with('thumbnail')
            ->whereNotIn('id', $registeredItemIds)
            ->when($search, fn($q) => $q->where('item_name', 'like', "%{$search}%"))
            ->paginate(100)
            ->withQueryString();

        return view('admin.newfaces.index', compact('newfaces', 'non_newfaces', 'search'));
    }

    // Ajax 並び順を更新
    public function sort(Request $request)
    {
        $newfaceOrder = $request->input('newfaceOrder');

        foreach ($newfaceOrder as $order) {
            Newface::where('id', $order['id'])->update(['sort' => $order['sort']]);
        }

        return response()->json(['success' => true]);
    }

    // Ajax 新人情報から削除（キャスト自体は削除しない）
    public function destroy($id)
    {
        $newface = Newface::find($id);

        if ($newface) {
            $newface->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => '新人情報が見つかりません']);
    }

    // 未登録キャストを一括登録
    public function bulkUpdate(Request $request)
    {
        $items = $request->input('items');

        foreach ($items as $itemId => $itemData) {
            if (isset($itemData['selected']) && $itemData['selected'] == 1) {
                $date = date('Y-m-d H:i:s');

                Newface::updateOrCreate(
                    ['item_id' => $itemId],
                    [
                        'date' => $date,
                        'sort' => 0,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', '新人情報を一斉登録しました');
    }

    // 一括処理：指定日より以前のキャストを新人情報から削除
    public function bulkAction(Request $request)
    {
        $request->validate([
            'before_date' => 'required|date',
            'action'      => 'required|in:remove',
        ]);

        $date = Carbon::parse($request->before_date)->endOfDay();

        $newfaces = Newface::where('date', '<', $date)->get();
        $count = $newfaces->count();

        if ($count === 0) {
            return back()->with('info', '該当する新人情報はありませんでした。');
        }

        Newface::where('date', '<', $date)->delete();

        return back()->with('success', "{$count}件の新人情報を削除しました（キャスト自体は削除されていません）。");
    }
}
