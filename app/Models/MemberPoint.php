<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'point',
        'status',
        'type',
        'payment_log_id',
        'expired_at',
        'related_type',
        'related_id',
        'memo',
        'created_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function paymentLog()
    {
        return $this->belongsTo(PaymentLog::class);
    }

    /**
     * type ラベル（日本語）
     * type が未設定の古いレコードは status で判定する。
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type ?? '') {
            'purchase'   => 'ポイント購入',
            'manual_add' => '手動追加',
            'manual_del' => '手動削除',
            'use'        => '使用',
            'expired'    => '期限切れ',
            'refund'     => '返還',
            default      => $this->status === 'add' ? '追加' : '使用',
        };
    }

    /**
     * type がポイント増加系かどうか
     */
    public function getIsAddAttribute(): bool
    {
        return in_array($this->type ?? '', ['purchase', 'manual_add', 'refund'], true)
            || ($this->status === 'add' && empty($this->type));
    }

    /** @deprecated getTypeLabelAttribute を使用 */
    public function getStatusTextAttribute(): string
    {
        return $this->type_label;
    }

    public function getPointDisplayAttribute(): string
    {
        $sign = $this->is_add ? '+' : '-';
        return $sign . number_format($this->point);
    }
}
