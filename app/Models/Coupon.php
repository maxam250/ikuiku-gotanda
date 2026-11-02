<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'main_image',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'date',
    ];

    // アイテムとの多対多リレーション
    public function items()
    {
        return $this->belongsToMany(Item::class, 'coupon_items', 'coupon_id', 'item_id')
                    ->withPivot('coupon_data')
                    ->withTimestamps();
    }

    // アクティブなクーポンのスコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 有効期限内のクーポンのスコープ
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', now());
        });
    }
}
