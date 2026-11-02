<?php

namespace App\Http\Controllers;

use App\Models\SpecialEvent;
use Illuminate\Http\Request;

class SpecialEventController extends Controller
{
    /**
     * 特設イベント一覧を表示
     */
    public function index()
    {
        $specialEvents = SpecialEvent::with(['items.thumbnail'])
            ->active()
            ->upcoming()
            ->orderBy('sort_order', 'asc')
            ->orderBy('event_date', 'asc')
            ->orderBy('start_hour', 'asc')
            ->orderBy('start_minute', 'asc')
            ->paginate(12);

        $breadcrumbs = [
            ['label' => 'ホーム', 'url' => '/'],
            ['label' => '特設イベント', 'url' => ''],
        ];

        return view('special-events.index', compact('specialEvents', 'breadcrumbs'));
    }

    /**
     * 特設イベント詳細を表示
     */
    public function show($id)
    {
        $specialEvent = SpecialEvent::with(['items.thumbnail'])
            ->active()
            ->upcoming()
            ->findOrFail($id);


        // 関連する特設イベントを取得
        $relatedEvents = SpecialEvent::with(['items.thumbnail'])
            ->active()
            ->upcoming()
            ->where('id', '!=', $id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('event_date', 'asc')
            ->orderBy('start_hour', 'asc')
            ->orderBy('start_minute', 'asc')
            ->limit(4)
            ->get();

        $breadcrumbs = [
            ['label' => 'ホーム', 'url' => '/'],
            ['label' => '特設イベント', 'url' => route('specialEvents.index')],
            ['label' => $specialEvent->title, 'url' => ''],
        ];

        return view('special-events.show', compact('specialEvent', 'relatedEvents', 'breadcrumbs'));
    }
}
