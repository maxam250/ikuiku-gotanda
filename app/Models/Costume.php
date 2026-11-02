<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costume extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'body',
        'cate',
        'sort',
        'filename',
        'active',
    ];

    /**
     * 属するカテゴリとのリレーションを定義
     */
    public function costume_cate()
    {
        return $this->belongsTo(CostumeCate::class, 'cate', 'id');
    }
}
