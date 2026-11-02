<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_publish',
        'name',
        'url',
        'sort',
        'filename',
        'image_url',
    ];
}
