<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRank extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'item_rank';

    // フィールドをfillableで指定
    protected $fillable = [
        'item_id',
        'rank_id',
        'no',
        'text',
    ];

    // 関連するRankモデル
    public function ranks()
    {
        return $this->belongsTo(Rank::class);
    }

    // 関連するItemモデル
    public function items()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }


}
