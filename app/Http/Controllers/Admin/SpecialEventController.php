<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialEventRequest;
use App\Models\SpecialEvent;
use App\Models\Event;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpecialEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specialEvents = SpecialEvent::with(['items'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('event_date', 'asc')
            ->orderBy('start_hour', 'asc')
            ->orderBy('start_minute', 'asc')
            ->paginate(20);

        return view('admin.special-events.index', compact('specialEvents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::where('item_active', 1)->orderBy('item_name', 'asc')->get();
        
        return view('admin.special-events.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpecialEventRequest $request)
    {
        $data = $request->validated();
        
        // 画像アップロード処理
        if ($request->hasFile('main_image')) {
            $image = $request->file('main_image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('special-events', $filename, 'public');
            $data['main_image'] = $path;
        }

        // item_idsをJSON形式で保存
        if ($request->has('item_ids')) {
            $data['item_ids'] = $request->input('item_ids');
        }

        $specialEvent = SpecialEvent::create($data);

        // アイテムとの関連付け
        if ($request->has('item_ids')) {
            $specialEvent->items()->sync($request->input('item_ids'));
        }

        return redirect()->route('admin.special-events.index')
            ->with('success', '特設イベントが正常に作成されました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(SpecialEvent $specialEvent)
    {
        $specialEvent->load(['items']);
        return view('admin.special-events.show', compact('specialEvent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpecialEvent $specialEvent)
    {
        $items = Item::where('item_active', 1)->orderBy('item_name', 'asc')->get();
        $specialEvent->load('items');
        
        // 関連付けられたアイテムのIDを取得
        $selectedItems = $specialEvent->items->pluck('id')->toArray();
        
        return view('admin.special-events.edit', compact('specialEvent', 'items', 'selectedItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpecialEventRequest $request, SpecialEvent $specialEvent)
    {
        $data = $request->validated();
        
        // 画像アップロード処理
        if ($request->hasFile('main_image')) {
            // 古い画像を削除
            if ($specialEvent->main_image) {
                Storage::disk('public')->delete($specialEvent->main_image);
            }
            
            $image = $request->file('main_image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('special-events', $filename, 'public');
            $data['main_image'] = $path;
        }

        // item_idsをJSON形式で保存
        if ($request->has('item_ids')) {
            $data['item_ids'] = $request->input('item_ids');
        }

        $specialEvent->update($data);

        // アイテムとの関連付けを更新
        if ($request->has('item_ids')) {
            $specialEvent->items()->sync($request->input('item_ids'));
        } else {
            $specialEvent->items()->detach();
        }

        return redirect()->route('admin.special-events.index')
            ->with('success', '特設イベントが正常に更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpecialEvent $specialEvent)
    {
        // 画像を削除
        if ($specialEvent->main_image) {
            Storage::disk('public')->delete($specialEvent->main_image);
        }

        // 関連付けを削除
        $specialEvent->items()->detach();
        
        $specialEvent->delete();

        return redirect()->route('admin.special-events.index')
            ->with('success', '特設イベントが正常に削除されました。');
    }

    /**
     * 特設イベントのステータスを切り替え
     */
    public function toggleStatus(SpecialEvent $specialEvent)
    {
        $specialEvent->update(['is_active' => !$specialEvent->is_active]);
        
        $status = $specialEvent->is_active ? '有効' : '無効';
        return redirect()->back()
            ->with('success', "特設イベントが{$status}になりました。");
    }
}
