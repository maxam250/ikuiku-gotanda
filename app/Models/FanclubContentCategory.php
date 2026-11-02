<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FanclubContentCategory extends Model
{
    use HasFactory;

    protected $table = 'fanclub_contents_categories';

    protected $fillable = [
        'cast_id',
        'name',
        'sort',
    ];

    public function cast(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'cast_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(FanclubContent::class, 'category_id');
    }
}

