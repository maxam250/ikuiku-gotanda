<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostumeCate extends Model
{
    use HasFactory;

    protected $table = 'costume_cates';

    // フィルラブル（mass assignmentを許可するカラム）の設定
    protected $fillable = [
        'active',
        'title',
        'text',
        'flag',
        'price',
        'sort',
     ];

    /**
     * このオプションカテゴリに属するオプションを取得するリレーション
     */
    public function costumes()
    {
        return $this->hasMany(Costume::class, 'cate');
    }

}
