<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * リレーション: キャンペーン
     */
    public function campaign()
    {
        // 外部キーは campaign_id
        return $this->belongsTo(NewsletterCampaign::class, 'campaign_id');
    }

    /**
     * リレーション: 購読者
     */
    public function subscriber()
    {
        return $this->belongsTo(NewsletterSubscriber::class, 'subscriber_id');
    }

    /**
     * バウンスかどうか
     */
    public function isBounced(): bool
    {
        return $this->status === 'bounced';
    }
}
