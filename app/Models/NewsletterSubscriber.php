<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'email_hash',
        'token',
        'verified_at',
        'unsubscribed_at',
        'ip_address',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * メールアドレスを暗号化して保存
     */
    public function setEmailAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['email'] = Crypt::encryptString($value);
            $this->attributes['email_hash'] = hash('sha256', strtolower($value));
        }
    }

    /**
     * メールアドレスを復号化して取得
     */
    public function getEmailAttribute()
    {
        $value = $this->attributes['email'] ?? null;
        if (empty($value)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            \Log::warning('Failed to decrypt email', [
                'subscriber_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return '[復号化失敗]';
        }
    }

    /**
     * 確認済みかどうか
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * 解除済みかどうか
     */
    public function isUnsubscribed(): bool
    {
        return $this->unsubscribed_at !== null;
    }

    /**
     * 有効な購読者かどうか（確認済みかつ未解除）
     */
    public function isActive(): bool
    {
        return $this->isVerified() && !$this->isUnsubscribed();
    }

    /**
     * トークンを生成
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * メールアドレスで検索（ハッシュを使用）
     */
    public static function findByEmail($email)
    {
        $hash = hash('sha256', strtolower($email));
        return self::where('email_hash', $hash)->first();
    }

    /**
     * リレーション: 配信ログ
     */
    public function logs()
    {
        return $this->hasMany(NewsletterLog::class, 'subscriber_id');
    }
}
