<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FanclubContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'cast_id',
        'category_id',
        'title',
        'description',
        'is_paid',
        'scheduled_at',
        'next_release_at',
        'published_at',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function cast(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'cast_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FanclubContentCategory::class, 'category_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(FanclubContentChild::class, 'fanclub_content_id')->orderBy('sort');
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
}

