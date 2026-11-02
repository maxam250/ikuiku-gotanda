<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'description',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function replaceVariables($member = null, $reservation = null)
    {
        $content = $this->content;

        if ($member) {
            $content = str_replace('{member_name}', $member->name ?? '', $content);
            $content = str_replace('{member_email}', $member->email ?? '', $content);
        }

        if ($reservation) {
            // 基本的な予約情報
            $content = str_replace('{reservation_date_mdw}', $reservation->date_mdw ?? '', $content);
            $content = str_replace('{reservation_date}', $reservation->date ?? '', $content);
            $content = str_replace('{reservation_time}', $reservation->time ?? '', $content);
            $content = str_replace('{reservation_datetime}',
                ($reservation->date ?? '') . ' ' . ($reservation->time_start ?? ''), $content);

            // コース情報
            if (isset($reservation->course)) {
                $courseName = $reservation->course->name ?? '';
                $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
                $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

                $content = str_replace('{course_name}', $courseName, $content);
                $content = str_replace('{course_time}', $courseTime, $content);
                $content = str_replace('{course_info}', $courseInfo, $content);
            } else {
                $content = str_replace('{course_name}', 'unko', $content);
                $content = str_replace('{course_time}', 'baka', $content);
                $content = str_replace('{course_info}', 'info', $content);
            }


            // キャスト情報
            if (isset($reservation->item)) {
                $content = str_replace('{cast_name}', $reservation->item->name ?? '', $content);
            } else {
                $content = str_replace('{cast_name}', '', $content);
            }

            // オプション・コスチューム情報
            $content = str_replace('{options}', is_array($reservation->option) ? implode(', ', $reservation->option) : ($reservation->option ?? ''), $content);
            $content = str_replace('{option_pays}', is_array($reservation->option_pay) ? implode(', ', $reservation->option_pay) : ($reservation->option_pay ?? ''), $content);
            $content = str_replace('{costumes}', is_array($reservation->costume) ? implode(', ', $reservation->costume) : ($reservation->costume ?? ''), $content);
            $content = str_replace('{myoptions}', $reservation->myoption ?? '', $content);
            $content = str_replace('{mycostumes}', $reservation->mycostume ?? '', $content);

            // 場所・エリア情報
            $content = str_replace('{area}', $reservation->area ?? '', $content);
            $content = str_replace('{place}', $reservation->place ?? '', $content);
            $content = str_replace('{meet}', $reservation->meet ?? '', $content);
            $content = str_replace('{address}', $reservation->address ?? '', $content);

            // 新しく追加する変数
            $content = str_replace('{comment}', $reservation->comment ?? '', $content);
            $content = str_replace('{memo_share}', $reservation->memo_share ?? '', $content);

            // 料金情報
            $content = str_replace('{fee_course}', $reservation->fee_course ?? '', $content);
            $content = str_replace('{fee_option}', $reservation->fee_option ?? '', $content);
            $content = str_replace('{fee_myoption}', $reservation->fee_myoption ?? '', $content);
            $content = str_replace('{fee_mycostume}', $reservation->fee_mycostume ?? '', $content);
            $content = str_replace('{fee_adjust}', $reservation->fee_adjust ?? '', $content);
            $content = str_replace('{fee_adjust_memo}', $reservation->fee_adjust_memo ?? '', $content);
            $content = str_replace('{fee_total}', $reservation->fee_total ?? '', $content);
            $feeTotal = $reservation->fee_total ?? 0;

            $feeTotalTax = number_format(round($feeTotal * config('tax.intax'))); // 四捨五入＋カンマ区切り

            $content = str_replace('{fee_total}', number_format($feeTotal), $content);
            $content = str_replace('{fee_total_tax}', $feeTotalTax, $content);


            // スタジオ情報（equipment）
            if (isset($reservation->equipment)) {
                $content = str_replace('{studio}', $reservation->equipment->name ?? '', $content);
            } else {
                $content = str_replace('{studio}', '', $content);
            }
        }

        return $content;
    }

    // スコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForMember($query)
    {
        return $query->where(function($q) {
            $q->where('type', 'member')
              ->orWhere('type', 'common');
        });
    }

    public function scopeForCast($query)
    {
        return $query->where(function($q) {
            $q->where('type', 'cast')
              ->orWhere('type', 'common');
        });
    }
}
