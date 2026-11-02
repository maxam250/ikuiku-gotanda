<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExternalItem extends Model
{
    use HasFactory;

    // ローカルサーバーのデータベース接続を指定（外部サーバーにはitemsテーブルが存在しないため）
    protected $connection = 'mysql';

    protected $table = 'items';

    protected $casts = [
        'email' => 'encrypted',
        'tel' => 'encrypted',
    ];

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * サムネイル
     */
    public function thumbnail()
    {
        return $this->hasOne(ExternalItemImage::class, 'item_id')->where('flag', 1);
    }

    /**
     * サムネイル ランキング用
     */
    public function th_flename()
    {
        // 1対1の関係からサムネイル画像を取得
        $image = $this->itemImage()->where('flag', 1)->first();

        // サムネイルがある場合はそのパスを返し、ない場合はデフォルト画像のパスを返す
        if ($image) {
            return Storage::url($image->filename);
        }
        return asset('images/dmy.jpg');
    }

    /**
     * スケジュール 1対多
     */
    public function schedules()
    {
        return $this->hasMany(ExternalSchedule::class, 'item_id', 'id');
    }

    /**
     * 予約
     */
    public function reservations()
    {
        return $this->hasMany(ExternalReservation::class, 'item_id', 'id');
    }

    /**
     * 画像 1対多
     */
    public function images()
    {
        return $this->hasMany(ExternalItemImg::class);
    }

    public function itemImage()
    {
        return $this->hasMany(ExternalItemImage::class, 'item_id', 'id');
    }

    /**
     * マイオプションとのリレーション
     * 外部サーバーにはitemsテーブルが存在しないため、ローカルサーバーから取得
     */
    public function myOptions()
    {
        return $this->hasMany(\App\Models\Option::class, 'item_id', 'id')->on('mysql');
    }

    /**
     * マイコスチュームとのリレーション
     * 外部サーバーにはitemsテーブルが存在しないため、ローカルサーバーから取得
     */
    public function myCostumes()
    {
        return $this->hasMany(\App\Models\Costume::class, 'item_id', 'id')->on('mysql');
    }
} 