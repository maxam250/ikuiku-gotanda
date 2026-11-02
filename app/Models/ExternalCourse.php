<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalCourse extends Model
{
    use HasFactory;

    // 外部サーバーのデータベース接続を指定
    protected $connection = 'external_mysql';

    protected $table = 'courses';

    protected $fillable = [
        'title',
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
        return $this->hasMany(ExternalReservation::class);
    }

    /**
     * コース料金とのリレーション
     */
    public function courseFees()
    {
        return $this->hasMany(ExternalCourseFee::class);
    }
} 