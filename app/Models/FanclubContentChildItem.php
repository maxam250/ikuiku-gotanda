<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FanclubContentChildItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fanclub_content_child_id',
        'type',
        'value',
        'sort',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(FanclubContentChild::class, 'fanclub_content_child_id');
    }
}
