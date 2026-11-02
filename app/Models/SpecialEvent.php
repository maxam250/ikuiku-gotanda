<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecialEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'event_date',
        'start_hour',
        'start_minute',
        'end_hour',
        'end_minute',
        'description',
        'item_ids',
        'main_image',
        'fee',
        'fee_note',
        'venue',
        'access_info',
        'map_url',
        'is_active',
        'external_link',
        'notes',
        'has_timer',
        'timer_start',
        'timer_end',
        'sort_order',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_hour' => 'integer',
        'start_minute' => 'integer',
        'end_hour' => 'integer',
        'end_minute' => 'integer',
        'item_ids' => 'array',
        'is_active' => 'boolean',
        'has_timer' => 'boolean',
        'timer_start' => 'date',
        'timer_end' => 'date',
        'fee' => 'integer',
    ];


    // アイテムとの多対多リレーション
    public function items()
    {
        return $this->belongsToMany(Item::class, 'items_special_events', 'special_event_id', 'item_id')
                    ->withTimestamps();
    }

    // アクティブな特設イベントのスコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 開催日が過ぎていないイベントのスコープ
    public function scopeUpcoming($query)
    {
        return $query->whereDate('event_date', '>=', Carbon::today());
    }

    // 日付順のスコープ
    public function scopeOrderByDate($query)
    {
        return $query->orderBy('event_date', 'asc')
                    ->orderBy('start_hour', 'asc')
                    ->orderBy('start_minute', 'asc');
    }

    // タイマー表示中のスコープ
    public function scopeWithTimer($query)
    {
        return $query->where('has_timer', true)
                    ->where('timer_start', '<=', now())
                    ->where('timer_end', '>=', now());
    }

    // 開始時間を取得するアクセサ
    public function getStartTimeAttribute()
    {
        return sprintf('%02d:%02d', $this->start_hour, $this->start_minute);
    }

    // 終了時間を取得するアクセサ
    public function getEndTimeAttribute()
    {
        return sprintf('%02d:%02d', $this->end_hour, $this->end_minute);
    }
}
