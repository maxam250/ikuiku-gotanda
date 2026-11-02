<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NewsletterCampaign extends Model
{
    protected $fillable = [
        'template_id',
        'subject',
        'body_text',
        'body_html',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
        'filter_status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'filter_status' => 'array',
    ];

    /**
     * リレーション: テンプレート
     */
    public function template()
    {
        return $this->belongsTo(NewsletterTemplate::class);
    }

    /**
     * リレーション: 配信ログ
     */
    public function logs()
    {
        // 外部キーは campaign_id
        return $this->hasMany(NewsletterLog::class, 'campaign_id');
    }

    /**
     * ステータスが予約済みかどうか
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * ステータスが送信中かどうか
     */
    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    /**
     * ステータスが完了かどうか
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * 配信可能かどうか
     */
    public function canSend(): bool
    {
        if ($this->status !== 'scheduled') {
            return false;
        }

        if ($this->scheduled_at && $this->scheduled_at->isFuture()) {
            return false;
        }

        return true;
    }
}
