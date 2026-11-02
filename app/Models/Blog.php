<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    //
    protected $fillable = [
        'title',
        'content',
        'main_image',
        'sub_images',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sub_images' => 'array',
    ];
}
