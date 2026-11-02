<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newface extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'sort',
        'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function itemImage()
    {
        return $this->hasOne(ItemImage::class, 'item_id', 'item_id')->thumbnailOrder();
    }
}
