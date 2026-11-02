<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankCate extends Model
{
    use HasFactory;

    // テーブル名を明示
    protected $table = 'rank_cates';

    // フィルラブル（mass assignmentを許可するカラム）の設定
    protected $fillable = ['active', 'title', 'body', 'sort'];

    /**
     * このオプションカテゴリに属するオプションを取得するリレーション
     */
    public function ranks()
    {
        return $this->hasMany(Rank::class, 'cate_id');
    }


}
