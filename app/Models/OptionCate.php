<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionCate extends Model
{
    use HasFactory;

    // 使用するテーブル名が 'option_cates' であることを明示
    protected $table = 'option_cates';

    // フィルラブル（mass assignmentを許可するカラム）の設定
    protected $fillable = [ 'title', 'body', 'sort'];

    /**
     * このオプションカテゴリに属するオプションを取得するリレーション
     */
    public function options()
    {
        return $this->hasMany(Option::class, 'cate');
    }


    public function option_pay()
    {
        return $this->hasMany(OptionPay::class, 'cate');
    }
}
