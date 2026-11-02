<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalEquipment extends Model
{
    use HasFactory;

    // 外部サーバーのデータベース接続を指定
    protected $connection = 'external_mysql';

    protected $table = 'equipments';

    protected $fillable = [
        'equipment_id',
        'name',
        'sort',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * 予約とのリレーション
     */
    public function reservations()
    {
        return $this->hasMany(ExternalReservation::class, 'equipment_id');
    }
} 