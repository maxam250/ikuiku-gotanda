<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    //
    public function index ()
    {

        // 表示位置 1=TOP&イベント / 3=イベントのみ（2=TOPのみ は出さない）
        $events = Event::whereIn('position', [1, 3])
        ->where(function ($query) {
            $query->where('timer_flag', 0)
                ->orWhere(function ($subQuery) {
                    $subQuery->where('timer_flag', 1)
                            ->where('date_start', '<=', Carbon::now())
                            ->where('date_end', '>=', Carbon::now());
                });
        })
        ->orderBy('sort', 'asc')
        ->orderBy('id', 'desc')
        ->get();


    return view('/event', compact('events'));
    }

}

