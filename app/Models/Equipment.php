<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';


    protected $fillable = [
        'active',
        'cate_id',
        'title',
        'text1',
        'text2',
        'text3',
        'text4',
        'text5',
        'flag1',
        'flag2',
        'flag3',
        'flag4',
        'flag5',
        'sort',
        'link',
        'filename',
        'filename_other',
        'map_url',
        'map_code',
        'external_id',
    ];


    /**
     * 属するカテゴリとのリレーションを定義
     */
    public function equipment_cate()
    {
        return $this->belongsTo(EquipmentCate::class, 'cate_id');
    }

    /**
     * 予約状況用のリレーション
     */
    public function reservations(){
        return $this->hasMany(Reservation::class, 'equipment_id');
    }

    /**
     * ホテルサブ画像とのリレーション
     */
    public function equipmentSubImages()
    {
        return $this->hasMany(EquipmentSubImage::class);
    }

    /**
     * 機材リスト画像とのリレーション
     */
    public function equipmentListImages()
    {
        return $this->hasMany(EquipmentListImage::class);
    }

    /**
     * 小物リスト画像とのリレーション
     */
    public function accessoryListImages()
    {
        return $this->hasMany(AccessoryListImage::class);
    }

}
