<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'cast_name',
    //     'cast_active',
    //     'cast_age',
    //     'cast_height',
    //     'cast_b',
    //     'cast_cup',
    //     'cast_w',
    //     'cast_h',
    //     'cast_comment',
    //     'cast_comment2',
    //     'cast_blogname',
    //     'cast_blog',
    //     'cast_twitter',
    //     'cast_url',
    //     ];

    protected $guarded = [

    ];


    // サムネイル
    public function thumbnail ()
    {
        return $this->hasOne('App\Models\CastImage', 'cast_id')->where('flag', 1);
    }


    // スケジュール 1対多
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'cast_id', 'id');
    }

    // 画像 1対多
    public function images()
    {
        return $this->hasMany(CastImg::class);
    }

    // ピックアップ 1対1
    public function pickup()
    {
        return $this->hasOne(Pickup::class, 'cast_id', 'id');
    }

    // 新着情報 1対1
    public function news()
    {
        return $this->belongsTo(News::class, $cast_id);
    }

    // オプション 多対多
    public function options()
    {
        return $this->belongsToMany(Option::class, 'cast_option', 'cast_id', 'option_id');
    }

}
