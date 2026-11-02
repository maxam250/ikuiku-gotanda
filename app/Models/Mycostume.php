<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mycostume extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'mycostume_cate_id',
        'name',
        'body',
        'filename',
        'active',
        'sort',
        'price'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function category()
    {
        return $this->belongsTo(MycostumeCate::class, 'mycostume_cate_id');
    }
}
