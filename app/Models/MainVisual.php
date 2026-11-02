<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MainVisual extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'pc_image_path',
        'mobile_image_path',
        'link_url',
        'is_active',
        'show_on_pc',
        'show_on_mobile',
        'start_date',
        'end_date',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_pc' => 'boolean',
        'show_on_mobile' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * PC用のアクティブなメインビジュアルを取得
     */
    public static function getActiveForPc()
    {
        return self::where('is_active', true)
            ->where('show_on_pc', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', Carbon::now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            })
            ->whereNotNull('pc_image_path')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * スマホ用のアクティブなメインビジュアルを取得
     */
    public static function getActiveForMobile()
    {
        return self::where('is_active', true)
            ->where('show_on_mobile', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', Carbon::now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            })
            ->whereNotNull('mobile_image_path')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * 表示期間内かどうかを判定
     */
    public function isInDisplayPeriod()
    {
        $now = Carbon::now();

        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }

    /**
     * PC表示可能かどうかを判定
     */
    public function canShowOnPc()
    {
        return $this->is_active
            && $this->show_on_pc
            && $this->pc_image_path
            && $this->isInDisplayPeriod();
    }

    /**
     * スマホ表示可能かどうかを判定
     */
    public function canShowOnMobile()
    {
        return $this->is_active
            && $this->show_on_mobile
            && $this->mobile_image_path
            && $this->isInDisplayPeriod();
    }

    /**
     * 次の並び順番号を取得
     */
    public static function getNextSortOrder()
    {
        return (self::max('sort_order') ?? 0) + 1;
    }

    /**
     * 並び順を更新
     */
    public static function updateSortOrder(array $items)
    {
        foreach ($items as $item) {
            self::where('id', $item['id'])
                ->update(['sort_order' => $item['position']]);
        }
    }
}
