<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class FanclubContentChild extends Model
{
    use HasFactory;

    protected $fillable = [
        'fanclub_content_id',
        'title',
        'description',
        'sort',
        'scheduled_at',
        'published_at',
        'teaser_enabled',
        'teaser_style',
        'teaser_image_path',
        'teaser_reveal_at',
        'teaser_promo_note',
    ];

    protected $casts = [
        'teaser_enabled' => 'boolean',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'teaser_reveal_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FanclubContent::class, 'fanclub_content_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FanclubContentChildItem::class, 'fanclub_content_child_id')->orderBy('sort');
    }

    public function teaserImageUrl(): ?string
    {
        if (!$this->teaser_image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->teaser_image_path);
    }

    public function scopeVisible($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
            ->whereNull('published_at');
    }

    /** ティザー演出が有効で、まだ本編公開前か */
    public function teaserBlocksMain(): bool
    {
        if (!$this->teaser_enabled) {
            return false;
        }

        if (!$this->teaser_reveal_at) {
            return false;
        }

        return now()->lt($this->teaser_reveal_at);
    }
}
