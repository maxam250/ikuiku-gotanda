<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    /** 保持期間（日）。この日数を過ぎた行は自動削除される。約3ヶ月。 */
    public const RETENTION_DAYS = 92;

    protected $table = 'access_logs';

    /** created_at のみ利用（updated_atは持たない） */
    public const UPDATED_AT = null;

    protected $fillable = [
        'visitor_id',
        'path',
        'referer',
        'referer_host',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
