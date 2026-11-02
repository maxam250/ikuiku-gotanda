<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;


    // フィルラブル（mass assignmentを許可するカラム）の設定
    protected $fillable = [ 'active', 'title', 'body', 'sort'];

    /**
     * このオプションカテゴリに属するオプションを取得するリレーション
     */
    public function course_fee()
    {
        return $this->hasMany(CourseFee::class, 'course_id');
    }

    /**
     * キャストとの多対多リレーション
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_course', 'course_id', 'item_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * キャストとの料金設定リレーション
     */
    public function itemFees()
    {
        return $this->belongsToMany(Item::class, 'item_course_fees', 'course_id', 'item_id')
                    ->withPivot('fee_60', 'fee_90', 'fee_120', 'fee_150', 'fee_180', 'fee_extension', 'uniform_rate', 'use_uniform_rate')
                    ->withTimestamps();
    }

    /**
     * キャスト削除時の処理
     * 関連する中間テーブルのレコードも削除
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($course) {
            // item_courseテーブルから関連レコードを削除
            \DB::table('item_course')->where('course_id', $course->id)->delete();
            
            // item_course_feesテーブルから関連レコードを削除
            \DB::table('item_course_fees')->where('course_id', $course->id)->delete();
        });
    }
}
