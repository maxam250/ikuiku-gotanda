<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;


    protected $casts = [
        'term_start' => 'date',
        'term_end' => 'date'
    ];

    protected $fillable = [
        'cate_id',
        'active',
        'title',
        'body',
        'filename',
        'sort',
        'position',
        'term_start',
        'term_end',
    ];


    /**
     * オプションが属するオプションカテゴリとのリレーションを定義
     */
    public function rank_cate()
    {
        return $this->belongsTo(RankCate::class, 'id', 'cate_id');
    }

    /**
     * キャストとの多対多リレーションを定義
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_rank', 'rank_id', 'item_id');
    }

    /**
     * item_rankとの多対多リレーションを定義
     */
    public function itemRanks()
    {
        return $this->hasMany(ItemRank::class, 'rank_id');
    }
}
