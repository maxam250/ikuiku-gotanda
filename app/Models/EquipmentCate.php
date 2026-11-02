<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentCate extends Model
{
    use HasFactory;

    // 使用するテーブル名が 'equipment_cates' であることを明示
    protected $table = 'equipment_cates';

    // フィルラブル（mass assignmentを許可するカラム）の設定
    protected $fillable = [
        'active',
        'title',
        'text',
        'flag',
        'link',
        'sort',
     ];

    /**
     * このオプションカテゴリに属するオプションを取得するリレーション
     */
    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'cate_id');
    }

}
