<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalReservation extends Model
{
    use HasFactory;

    // 外部サーバーのデータベース接続を指定
    protected $connection = 'external_mysql';

    protected $table = 'reservations';

    protected $fillable = [
        'name',
        'member_id',
        'email',
        'tel',
        'kind',
        'item_name',
        'item_name_other',
        'item_id',
        'equipment_id',
        'date',
        'time_start',
        'time_end',
        'course_id',
        'course_time',
        'option',
        'option_pay',
        'costume',
        'myoption',
        'mycostume',
        'comment',
        'memo',
        'memo_share',
        'memo_result',
        'fee_course',
        'fee_option',
        'fee_myoption',
        'fee_mycostume',
        'fee_adjust',
        'fee_adjust_memo',
        'fee_total',
        'area',
        'place',
        'meet',
        'address',
        'flag',
        'staff_id',
        'reserved_at',
        'status',
        'shop_id',
        'gender',
        'p_category',
        'p_plan',
        'p_option',
        'background',
        'make_plan',
        'make_flag',
        'make_status',
        'outsourcing_id',
        'p_outsourcing',
        'payment',
        'payment_flag',
        'place_status',
        'useage',
        'publish',
        'reference1',
        'reference2',
        'filename1',
        'filename2',
        'corporate_name',
        'color_code',
        'estimated_duration',
        'guest_count',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'date' => 'date',
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'email' => 'encrypted',
        'tel' => 'encrypted',
        'option' => 'array',
        'option_pay' => 'array',
        'costume' => 'array'
    ];

    protected $guarded = [];

    /**
     * 会員情報との多対多リレーションを定義
     */
    public function member()
    {
        return $this->belongsTo(ExternalMember::class, 'member_id');
    }

    /**
     * キャストとスケジュールの1対多
     * 外部サーバーにはitemsテーブルが存在しないため、ローカルサーバーから取得
     */
    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id', 'id');
    }

    /**
     * スケジュール情報との1対多リレーション
     * 外部サーバーにはschedulesテーブルが存在するが、itemsテーブルがないため無効化
     */
    public function schedule()
    {
        return $this->belongsTo(\App\Models\Schedule::class, 'item_id', 'item_id')
            ->whereColumn('reservations.date', 'schedules.date');
    }

    /**
     * 予約状況用のリレーション
     */
    public function equipment()
    {
        return $this->belongsTo(ExternalEquipment::class);
    }

    /**
     * コース情報とのリレーション
     * 外部サーバーにはcoursesテーブルが存在しないため、ローカルサーバーから取得
     */
    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'course_id', 'id');
    }

    /**
     * 事業所とのリレーション
     */
    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class, 'shop_id', 'idname');
    }

    /**
     * スタッフとのリレーション
     */
    public function staff()
    {
        return $this->belongsTo(ExternalStaff::class);
    }
}
