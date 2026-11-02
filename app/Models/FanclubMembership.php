<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ファンクラブ参加状態
 *
 * status:
 *   - active   : 参加中
 *   - paused   : 一時停止（管理者による）
 *   - canceled : 退会
 *
 * 単発決済への移行に伴い、以下のカラムは削除済み:
 *   - payment_status      (Subscription課金ステータス)
 *   - next_billing_at     (次回課金日、月額前提)
 *   - square_subscription_id (Square Subscription ID)
 *
 * 決済情報は payment_logs テーブルで管理する。
 */
class FanclubMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'fanclub_id',
        'member_id',
        'status',
        'payment_method',
        'started_at',
        'ended_at',
        'last_paid_at',
        'square_customer_id',
        'admin_note',
        'warning_sent_at',
        'warning_count',
        'suspended_until',
        'suspension_reason',
    ];

    protected $casts = [
        'started_at'     => 'datetime',
        'ended_at'       => 'datetime',
        'last_paid_at'   => 'datetime',
        'warning_sent_at' => 'datetime',
        'suspended_until' => 'datetime',
    ];

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class, 'fanclub_id', 'fanclub_id')
            ->where('member_id', $this->member_id ?? 0);
    }

    public function isSuspended(): bool
    {
        return $this->suspended_until !== null && $this->suspended_until->isFuture();
    }

    public function fanclub(): BelongsTo
    {
        return $this->belongsTo(Fanclub::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
