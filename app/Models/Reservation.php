<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\Shop;
use App\Models\Staff;
use App\Models\Outsourcing;
use App\Models\Equipment;

class Reservation extends Model
{
    use HasFactory;

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
        // 'time_start' => 'datetime:H:i',
        // 'time_end' => 'datetime:H:i',
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'email' => 'encrypted',
        'tel' => 'encrypted',
        'option' => 'array',
        'option_pay' => 'array',
        'costume' => 'array'
    ];

    protected $guarded = [
    ];


    /**
     * 会員情報との多対多リレーションを定義
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * キャストとスケジュールの1対多
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }


    /**
     * スケジュール情報との1対多リレーション
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'item_id', 'item_id')
            ->whereColumn('reservations.date', 'schedules.date');
    }


    /**
     * 予約状況用のリレーション
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * コース情報とのリレーション
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * キャスト名を取得するアクセサ
     */
    public function getCastNameAttribute()
    {
        return $this->item ? $this->item->item_name : 'キャスト名未設定';
    }

    /**
     * 事業所とのリレーション
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    /**
     * 外注担当とのリレーション
     */
    public function outsourcing()
    {
        return $this->belongsTo(Outsourcing::class);
    }

    /**
     * スタッフとのリレーション
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * 予約重複チェック
     */
    public static function checkOverlap($equipmentId, $date, $timeStart, $timeEnd, $excludeId = null)
    {
        $equipment = Equipment::find($equipmentId);

        // 出張撮影の場合は重複を許可
        if ($equipment && $equipment->is_location) {
            return false; // 重複なしとして扱う
        }

        $query = self::where('equipment_id', $equipmentId)
            ->where('date', $date)
            ->where('flag', '!=', 3) // 保留以外
            ->where(function($q) use ($timeStart, $timeEnd) {
                $q->where(function($q) use ($timeStart, $timeEnd) {
                    // 開始時間が既存予約の時間内
                    $q->where('time_start', '<', $timeEnd)
                      ->where('time_end', '>', $timeStart);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * 予約重複バリデーションルール
     */
    public static function getOverlapValidationRule($equipmentId, $date, $timeStart, $timeEnd, $excludeId = null)
    {
        return function($attribute, $value, $fail) use ($equipmentId, $date, $timeStart, $timeEnd, $excludeId) {
            if (self::checkOverlap($equipmentId, $date, $timeStart, $timeEnd, $excludeId)) {
                $fail('この時間帯は既に予約が入っています。');
            }
        };
    }

    /**
     * 時間バリデーション
     */
    public static function validateTimeRange($timeStart, $timeEnd)
    {
        $errors = [];

        // 時間形式のチェック
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $timeStart)) {
            $errors[] = '開始時間の形式が正しくありません。';
        }

        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $timeEnd)) {
            $errors[] = '終了時間の形式が正しくありません。';
        }

        if (empty($errors)) {
            // 時間の比較
            $start = \Carbon\Carbon::parse($timeStart);
            $end = \Carbon\Carbon::parse($timeEnd);

            if ($start->gte($end)) {
                $errors[] = '開始時間は終了時間より前である必要があります。';
            }
        }

        return $errors;
    }

    /**
     * 予約重複チェック（拡張版）
     * スタジオとキャストの重複をチェック
     */
    public static function checkReservationOverlap($equipmentId, $itemId, $date, $timeStart, $timeEnd, $shopId, $excludeId = null)
    {
        $errors = [];

        // 1. スタジオの重複チェック（出張撮影カテゴリは対象外）
        if ($equipmentId) {
            $equipment = Equipment::find($equipmentId);

            // 出張撮影でない場合のみチェック
            if (!$equipment || !$equipment->is_location) {
                $studioOverlap = self::where('equipment_id', $equipmentId)
                    ->where('date', $date)
                    ->where('flag', '!=', 3) // 保留以外
                    ->where(function($q) use ($timeStart, $timeEnd) {
                        $q->where(function($q) use ($timeStart, $timeEnd) {
                            // 開始時間が既存予約の時間内
                            $q->where('time_start', '<', $timeEnd)
                              ->where('time_end', '>', $timeStart);
                        });
                    });

                if ($excludeId) {
                    $studioOverlap->where('id', '!=', $excludeId);
                }

                if ($studioOverlap->exists()) {
                    $errors[] = 'このスタジオは既に予約が入っています。';
                }
            }
        }

        // 2. kirari_kirariの場合、キャストの重複チェック
        if ($itemId && $shopId) {
            // shop_idからShopを取得してkirariかどうかチェック
            $shop = \App\Models\Shop::where('idname', $shopId)->first();
            if ($shop && $shop->idname === 'kirari') {
                $castOverlap = self::where('item_id', $itemId)
                    ->where('date', $date)
                    ->where('flag', '!=', 3) // 保留以外
                    ->where(function($q) use ($timeStart, $timeEnd) {
                        $q->where(function($q) use ($timeStart, $timeEnd) {
                            // 開始時間が既存予約の時間内
                            $q->where('time_start', '<', $timeEnd)
                              ->where('time_end', '>', $timeStart);
                        });
                    });

                if ($excludeId) {
                    $castOverlap->where('id', '!=', $excludeId);
                }

                if ($castOverlap->exists()) {
                    $errors[] = 'このキャストは既に予約が入っています。';
                }
            }
        }

        return $errors;
    }

    /**
     * 予約重複バリデーションルール（拡張版）
     */
    public static function getReservationOverlapValidationRule($equipmentId, $itemId, $date, $timeStart, $timeEnd, $shopId, $excludeId = null)
    {
        return function($attribute, $value, $fail) use ($equipmentId, $itemId, $date, $timeStart, $timeEnd, $shopId, $excludeId) {
            $errors = self::checkReservationOverlap($equipmentId, $itemId, $date, $timeStart, $timeEnd, $shopId, $excludeId);

            if (!empty($errors)) {
                $fail(implode(' ', $errors));
            }
        };
    }

}
