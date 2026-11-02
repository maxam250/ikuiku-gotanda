<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'cate',
        'title',
        'body',
        'sort',
        'filename',
    ];


    /**
     * オプションが属するオプションカテゴリとのリレーションを定義
     */
    public function option_cate()
    {
        return $this->belongsTo(OptionCate::class, 'id', 'cate');
    }

    /**
     * キャストとの多対多リレーションを定義
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_option', 'option_id', 'item_id');
    }
}
