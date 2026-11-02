<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionPay extends Model
{
    use HasFactory;

    protected $fillable = [
        'cate',
        'title',
        'body',
        'sort',
        'price',
        'filename',
    ];


    /**
     * オプションが属するオプションカテゴリとのリレーションを定義
     */
    public function option_cate()
    {
        return $this->belongsTo(OptionCate::class, 'id', 'cate');
    }

}
