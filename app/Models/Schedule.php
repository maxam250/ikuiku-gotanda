<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Schedule extends Model
{
    use HasFactory;

    protected $casts = [
        // 'date' => 'datetime',
        // time_start / time_end は 24時超(28:00等)の値を含むため datetime cast しない
    ];


    // スケジュール更新時に親Itemのupdated_atも更新する
    protected $touches = ['item'];

    protected $fillable = [
        'item_id',
        'date',
        'time_start',
        'time_end',
        'ts_start',
        'ts_end',
        'status',
        'sort',
        'flag',
        // 他のフィールドも必要に応じて追加
    ];


    /**
     * 開始時間をセットする際の処理
     *
     * @param datetime $value Y-m-d H:i:s
     *
     */
    public function setTimeStartAttribute($value)
    {

        // 時間を `H:i:s` フォーマットで保存
        $this->attributes['time_start'] = $value;

        // タイムスタンプに変換して保存
        $this->attributes['ts_start'] = $this->timeToTimestamp($value);
    }


    /**
     * 終了時間をセットする際の処理
     *
     * @param datetime $value Y-m-d H:i:s
     *
     */
    public function setTimeEndAttribute($value)
    {
        // 時間を `H:i:s` フォーマットで保存
        $this->attributes['time_end'] = $value;

        // タイムスタンプに変換して保存
        $this->attributes['ts_end'] = $this->timeToTimestamp($value);
    }


    /**
     * 時間をタイムスタンプに変換するメソッド
     *
     * @param datetime $value Y-m-d H:i:s
     * @return timestamp
     */
    protected function timeToTimestamp($time)
    {
        // 時間が24時以上の場合の調整
        $parts = explode(':', $time);
        $hours = (int)$parts[0];
        $minutes = (int)$parts[1];
        $seconds = isset($parts[2]) ? (int)$parts[2] : 0;

        // 24時以降の調整 (例: 25:00をタイムスタンプ 90000 秒として保存)
        if ($hours >= 24) {
            $hours -= 24;
            return ($hours * 3600) + ($minutes * 60) + $seconds + 86400; // 24時間（86400秒）を加算
        }

        // 通常の時間の場合
        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }



    // リレーション設定

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }


    public function itemImage()
    {
        return $this->hasOne(ItemImage::class, 'item_id', 'item_id');
    }

    public function itemImages()
    {
        return $this->hasMany(ItemImage::class, 'item_id', 'item_id')->orderBy('sort');
    }

    // 予約時間を取得
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'item_id', 'item_id');
    }
}
