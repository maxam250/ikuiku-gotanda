<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 決済ログ（単発決済・ポイント購入共通）
 *
 * purchase_type:
 *   - fanclub_join    : ファンクラブ参加決済
 *   - point_purchase  : ポイント購入決済
 *
 * status:
 *   - pending  : 決済リクエスト作成済み、Webhook未受信
 *   - paid     : 決済成功・特典反映済み
 *   - failed   : 決済失敗
 *   - refunded : 返金済み
 *
 * 二重処理防止:
 *   - square_payment_id は UNIQUE 制約付き（同一決済の重複ログを防止）
 *   - processed_at が NULL でない場合は特典反映済み（Webhookの冪等性保証）
 */
class PaymentLog extends Model
{
    protected $fillable = [
        'member_id',
        'fanclub_id',
        'square_payment_id',
        'amount',
        'purchase_type',
        'status',
        'points',
        'payload',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'payload'      => 'array',
        'processed_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function fanclub(): BelongsTo
    {
        return $this->belongsTo(Fanclub::class);
    }

    /**
     * Webhook処理済みかどうか（二重処理防止の判定）
     */
    public function isProcessed(): bool
    {
        return $this->processed_at !== null;
    }

    /**
     * ファンクラブ参加決済かどうか
     */
    public function isFanclubJoin(): bool
    {
        return $this->purchase_type === 'fanclub_join';
    }

    /**
     * ポイント購入決済かどうか
     */
    public function isPointPurchase(): bool
    {
        return $this->purchase_type === 'point_purchase';
    }
}
