<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Item extends  Authenticatable
{
    use HasFactory, Notifiable;

    // protected $fillable = [
    //     'item_name',
    //     'item_active',
    //     'item_age',
    //     'item_height',
    //     'item_b',
    //     'item_cup',
    //     'item_w',
    //     'item_h',
    //     'item_comment',
    //     'item_comment2',
    //     'item_blogname',
    //     'item_blog',
    //     'item_twitter',
    //     'item_url',
    //     ];
    protected $casts = [
        'item_p_comment_date' => 'datetime',
        // default_sch_start / default_sch_end は 24時超(35:00等)の値を含むため datetime cast しない
    ];

    protected $guarded = [

    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    // サムネイル
    public function thumbnail ()
    {
        // チェック済みの画像、無ければ先頭（sort 昇順）の画像
        return $this->hasOne('App\Models\ItemImage', 'item_id')->thumbnailOrder();
    }

    // サムネイル ランキング用
    public function th_flename()
    {
        // 1対1の関係からサムネイル画像を取得
        $image = $this->itemImage()->thumbnailOrder()->first();

        // サムネイルがある場合はそのパスを返し、ない場合はデフォルト画像のパスを返す
        if ($image) {
            return Storage::url( $image->filename);
        }
        return asset('images/dmy.jpg');
    }

    // スケジュール 1対多
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'item_id', 'id');
    }

    // 予約
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'item_id', 'id');
    }

    // 画像 1対多
    public function images()
    {
        return $this->hasMany(ItemImg::class);
    }

    public function itemImage()
    {
        return $this->hasOne(ItemImage::class);
    }

    // ピックアップ 1対1
    public function pickup()
    {
        return $this->hasOne(Pickup::class, 'item_id', 'id');
    }

    // 新着情報 1対1
    public function news()
    {
        return $this->belongsTo(News::class, $item_id);
    }

    // オプション 多対多
    public function options()
    {
        return $this->belongsToMany(Option::class, 'item_option', 'item_id', 'option_id');
    }

    // オプションPay 多対多
    public function option_pays()
    {
        return $this->belongsToMany(OptionPay::class, 'item_option_pay', 'item_id', 'option_pay_id');
    }


    // プロフィール 多対多
    public function profiles()
    {

       return $this->belongsToMany(Profile::class, 'item_profile')
                    ->withPivot('content') // 中間テーブルのカラム
                    ->withTimestamps(); // 中間テーブルにタイムスタンプを使う場合

    }

    // オプションPay 多対多
    public function icon_items()
    {
        // 登録順（旧サイトは1つ目を左上・2つ目を右下に表示）
        return $this->belongsToMany(IconItem::class, 'item_icon_item', 'item_id', 'item_icon_id')
            ->orderBy('item_icon_item.id');
    }

    // ランキング 多対多
    public function ranks()
    {
        return $this->belongsToMany(Rank::class, 'item_rank', 'item_id', 'rank_id');

    }



    // マイオプション
    public function myoptions()
    {
        return $this->hasMany(Myoption::class);
    }

    // マイコスチューム
    public function mycostumes()
    {
        return $this->hasMany(Mycostume::class);
    }
    // Q&A
    public function interviews()
    {
        return $this->belongsToMany(Interview::class, 'item_interview', 'item_id', 'interview_id')
                    ->withPivot('content')
                    ->withTimestamps();
    }

    // コース 多対多
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'item_course', 'item_id', 'course_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // コース料金設定 多対多
    public function courseFees()
    {
        return $this->belongsToMany(Course::class, 'item_course_fees', 'item_id', 'course_id')
                    ->withPivot('fee_60', 'fee_90', 'fee_120', 'fee_150', 'fee_180', 'fee_extension', 'uniform_rate', 'use_uniform_rate')
                    ->withTimestamps();
    }

    // 特設イベント　多対多
    public function specialEvents()
    {
        return $this->belongsToMany(SpecialEvent::class, 'items_special_events', 'item_id', 'special_event_id')
                    ->withTimestamps();
    }

    // クーポン 多対多
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_items', 'item_id', 'coupon_id')
                    ->withPivot('coupon_data')
                    ->withTimestamps();
    }

    /**
     * メッセージアイテムリレーション（送信メッセージ）
     */
    public function sentMessageItems()
    {
        return $this->hasMany(MessageItem::class, 'sender_id', 'id')
            ->where('sender_type', 'item');
    }

    /**
     * メッセージアイテムリレーション（受信メッセージ）
     */
    public function receivedMessageItems()
    {
        return $this->hasMany(MessageItem::class, 'receiver_id', 'id')
            ->where('receiver_type', 'item');
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function premiumRank()
    {
        return $this->belongsTo(PremiumRank::class, 'premium_rank_id');
    }

    public function fanclub()
    {
        return $this->hasOne(Fanclub::class, 'cast_id');
    }

    public function fanclubCategories()
    {
        return $this->hasMany(FanclubContentCategory::class, 'cast_id');
    }

    public function fanclubContents()
    {
        return $this->hasMany(FanclubContent::class, 'cast_id');
    }

    /**
     * 未読メッセージ数のアクセサ
     */
    public function getUnreadCountAttribute()
    {
        return $this->receivedMessageItems()
            ->where('is_read', false)
            ->count();
    }

}
