<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Pickup;
use Carbon\Carbon;

class PickupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // itemsテーブルに存在するitem_idのみを取得
        $validItemIds = Item::pluck('id')->toArray();


        // 有効なitem_idを持つpickupsを取得
        $pickups = Pickup::with(['item', 'itemImage'])
        ->whereIn('item_id', $validItemIds)
        ->orderBy('sort', 'asc')
        ->get();



        // ピックアップしているキャストのIDリストを取得
        $workingItemIds = $pickups->pluck('item_id')->toArray();




        // ピックアップしていないキャストを取得
        $non_pickups = Item::with('thumbnail')->selectable()
        ->whereNotIn('id', $workingItemIds)
        ->when($search, fn($q) => $q->where('item_name', 'like', "%{$search}%"))
        ->paginate(100)
        ->withQueryString();


        return view('admin.pickups.index', compact('pickups', 'non_pickups', 'search'));


    }



    // Ajax　並び順を更新する処理
    public function sort(Request $request)
    {
        $pickupOrder = $request->input('pickupOrder');

        foreach ($pickupOrder as $order) {
            // 各スケジュールの並び順を更新
            pickup::where('id', $order['id'])->update(['sort' => $order['sort']]);
        }

        return response()->json(['success' => true]);
    }



    //Ajax　ピックアップから削除する処理
    public function destroy($id)
    {
        $pickup = pickup::find($id);

        if ($pickup) {
            $pickup->delete(); // スケジュールを削除
            return response()->json(['success' => true]);

        } else {
            return response()->json(['success' => false, 'message' => 'スケジュールが見つかりません']);
        }
    }



    // 非ピックアップキャストを一括ピックアップ
    public function bulkUpdate(Request $request)
    {
        $items = $request->input('items');


        // スケジュールを更新または作成
        foreach ($items as $itemId => $itemData) {

            // チェックが入っているキャストのみ処理
            if (isset($itemData['selected']) && $itemData['selected'] == 1) {
            $date = date('Y-m-d H:i:s');


                // ピックアップスケジュールを作成または更新
                pickup::updateOrCreate(
                    ['item_id' => $itemId], // キャストIDと日付で一意にする
                    [
                        'date' => $date,
                        'sort' => 0   // 0を固定で入力
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'ピックアップ情報を一斉登録しました');
    }

}
