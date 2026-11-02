<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'body',
        'sort',
        'filename',
        'position',
        'cate',
        'link',
        'timer_flag',
        'date_start',
        'date_end',
        // 他のフィールドも必要に応じて追加
    ];



}
