<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ExternalMember extends Authenticatable
{
    use HasFactory, Notifiable;

    // 外部サーバーのデータベース接続を指定
    protected $connection = 'external_mysql';

    protected $table = 'members';

    protected $fillable = [
        'name',
        'point',
        'email',
        'loginid',
        'password',
        'birth',
        'gender',
        'kind',
        'check',
        'tel',
        'line',
        'sns',
        'url',
        'memo',
        'memo_share',
        'nickname',
        'member_no',
        'filename',
        'favo',
        'block',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth' => 'date',
        'name' => 'encrypted',
        'nickname' => 'encrypted',
        'tel' => 'encrypted',
    ];

    /**
     * 予約履歴用のリレーション
     */
    public function reservations()
    {
        return $this->hasMany(ExternalReservation::class, 'member_id');
    }
} 