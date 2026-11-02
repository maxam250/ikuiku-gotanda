<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'inquiry_type',
        'subject',
        'message',
        'status',
        'admin_memo',
        'ip_address',
        'user_agent',
        'is_spam',
    ];

    protected $casts = [
        'is_spam' => 'boolean',
        'email' => 'encrypted',
        'phone' => 'encrypted',
    ];

    // 問い合わせ種別の選択肢
    public static function getInquiryTypes()
    {
        return [
            'general' => '一般的なお問い合わせ',
            'reservation' => '予約について',
            'cast' => 'キャストについての問い合わせ',
            'other' => 'その他',
        ];
    }

    // ステータスの選択肢
    public static function getStatuses()
    {
        return [
            'unread' => '未読',
            'in_progress' => '対応中',
            'completed' => '完了',
        ];
    }

    // 問い合わせ種別の表示名を取得
    public function getInquiryTypeLabelAttribute()
    {
        return self::getInquiryTypes()[$this->inquiry_type] ?? $this->inquiry_type;
    }

    // ステータスの表示名を取得
    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
}
