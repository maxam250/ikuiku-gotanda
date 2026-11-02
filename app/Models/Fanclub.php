<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fanclub extends Model
{
    use HasFactory;

    protected $fillable = [
        'cast_id',
        'title',
        'description',
        'main_image_path',
        'price',
        'status',
        
    ];

    public function cast(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'cast_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(FanclubMembership::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(FanclubContentCategory::class, 'cast_id', 'cast_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(FanclubContent::class, 'cast_id', 'cast_id');
    }
}

