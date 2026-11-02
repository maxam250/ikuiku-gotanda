<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];

    protected $fillable = [
        'active',
        'cate',
        'title',
        'body',
        'link',
        'filename',
        'timer_flag',
        'date_start',
        'date_end',
    ];

    /**
     * フロント表示用: active=1 かつ、タイマー未使用 or 掲載期間内。
     * 開始・終了が null の場合はその側の制限なし（終了のみ未設定なら掲載継続）。
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('active', 1)
            ->where(function ($q) use ($now) {
                $q->where('timer_flag', 0)
                    ->orWhere(function ($sq) use ($now) {
                        $sq->where('timer_flag', 1)
                            ->where(function ($w) use ($now) {
                                $w->whereNull('date_start')
                                    ->orWhere('date_start', '<=', $now);
                            })
                            ->where(function ($w) use ($now) {
                                $w->whereNull('date_end')
                                    ->orWhere('date_end', '>=', $now);
                            });
                    });
            });
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

}
