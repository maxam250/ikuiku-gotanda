<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body_text',
        'body_html',
    ];

    /**
     * リレーション: キャンペーン
     */
    public function campaigns()
    {
        return $this->hasMany(NewsletterCampaign::class);
    }
}
