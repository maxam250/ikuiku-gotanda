<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    use HasFactory;

    protected $table = 'item_images'; // テーブル名を指定

    protected $fillable = ['item_id', 'filename', 'flag', 'sort'];

    public function item()
    {
        return $this->belongsTo(Item::class); // Item を大文字に修正
    }

    /**
     * サムネイルの優先順：サムネイルにチェック（flag=1）した画像 → 無ければ先頭（管理画面で一番左＝sort 昇順）の画像
     */
    public function scopeThumbnailOrder($query)
    {
        return $query->orderByDesc('flag')->orderBy('sort')->orderBy('id');
    }
}