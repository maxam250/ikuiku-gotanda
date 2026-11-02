<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'sort',
    ];

    // キャストとの多対多のリレーション
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_interview') // 中間テーブル名とキーの指定
                    ->withPivot('content') // 中間テーブルの 'content' カラムを取得
                    ->withTimestamps();
    }
}
