<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalSchedule extends Model
{
    use HasFactory;

    // 外部サーバーのデータベース接続を指定
    protected $connection = 'external_mysql';

    protected $table = 'schedules';

    protected $fillable = [
        'item_id',
        'date',
        'time_start',
        'time_end',
        'ts_start',
        'ts_end',
        'request_time_start',
        'request_time_end',
        'request_ts_start',
        'request_ts_end',
        'status',
        'sort',
        'flag',
    ];

    protected $casts = [
        'date' => 'date',
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'request_time_start' => 'datetime',
        'request_time_end' => 'datetime',
    ];

    /**
     * アイテムとのリレーション
     * 外部サーバーにはitemsテーブルが存在しないため、ローカルサーバーから取得
     */
    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id')->on('mysql');
    }

    /**
     * 予約とのリレーション
     */
    public function reservations()
    {
        return $this->hasMany(ExternalReservation::class, 'item_id', 'item_id')
            ->whereColumn('reservations.date', 'schedules.date');
    }
} 