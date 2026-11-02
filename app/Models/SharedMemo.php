<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SharedMemo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'staff_id',
        'memo_date',
        'title',
        'body',
        'url',
    ];

    protected $casts = [
        'memo_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(SharedMemoCategory::class, 'category_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
