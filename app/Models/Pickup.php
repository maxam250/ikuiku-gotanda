<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    use HasFactory;


    protected $fillable = [
        'item_id',
        'sort',
        'date',
        // 他のフィールドも必要に応じて追加
    ];


    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id',);
    }

    public function itemImage()
    {
        return $this->hasOne(ItemImage::class, 'item_id', 'item_id');
    }

}
