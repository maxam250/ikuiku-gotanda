<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumRank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fee',
        'fee_sub',
        'note',
        'sort',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'premium_rank_id');
    }
}
