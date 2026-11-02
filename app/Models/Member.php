<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

class Member extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'point',
        'email',
        'loginid',
        'password',
        'birth',
        'gender',
        'kind',
        'check',
        'tel',
        'line',
        'sns',
        'url',
        'memo',
        'memo_share',
        'nickname',
        'member_no',
        'filename',
        'favo',
        'block',
        'square_customer_id',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth' => 'date',
        // 'name' => 'encrypted', // 手動復号化に変更
        // 'nickname' => 'encrypted',
        // 'tel' => 'encrypted', // 手動復号化に変更
        // 'birth' => 'encrypted',
        'last_message_at' => 'datetime',

    ];

    // //emailの暗号化
    // public function getEmailAttribute($value)
    // {
    //     return openssl_decrypt($value, 'AES-128-ECB', '暗号化キー');
    // }
    // public function setEmailAttribute($value)
    // {
    //     $this->attributes['email'] = openssl_encrypt($value, 'AES-128-ECB', '暗号化キー');
    // }

    /**
     * Get the attributes that should be item.
     *
     * @return array<string, string>
     */
    protected function items(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 予約　履歴用

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // ポイント用
    public function memberPoints()
    {
        return $this->hasMany(MemberPoint::class, 'member_id', 'id');
    }

    // ポイント合計のアクセサ
    public function getTotalPointsAttribute()
    {
        return $this->memberPoints->reduce(function ($carry, $point) {
            if ($point->status === 'add') {
                return $carry + $point->point;
            } elseif ($point->status === 'del') {
                return $carry - $point->point;
            }
            return $carry;
        }, 0);
    }
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MemberResetPasswordNotification($token));
    }

    /**
     * Get the email address that should be used for password reset.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        try {
            $email = $this->getRawOriginal('email');
            if (empty($email)) {
                return '';
            }
            return Crypt::decryptString($email);
        } catch (\Exception $e) {
            \Log::warning('Failed to decrypt email for password reset - Member ID: ' . $this->id . ' - ' . $e->getMessage());
            return $this->getRawOriginal('email') ?: ''; // 暗号化されていない場合はそのまま返す
        }
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->getEmailForPasswordReset();
    }

    /**
     * Get the email address that should be used for password reset.
     * This method is called by the password reset broker.
     *
     * @return string
     */
    public function getEmailForPasswordResetAttribute()
    {
        return $this->getEmailForPasswordReset();
    }

    /**
     * メッセージリレーション（送信メッセージ）
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'id')
            ->where('sender_type', 'member');
    }

    /**
     * メッセージリレーション（受信メッセージ）
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'id')
            ->where('receiver_type', 'member');
    }

    /**
     * 未読メッセージ数のアクセサ（管理者視点）
     * 会員が送信して管理者が受信した未読メッセージ数をカウント
     */
    public function getUnreadCountAttribute()
    {
        return $this->sentMessages()
            ->where('receiver_type', 'admin')
            ->where('is_read', false)
            ->count();
    }

    public function fanclubMemberships()
    {
        return $this->hasMany(FanclubMembership::class, 'member_id');
    }

}

//Auth::attempt(['loginid' => ..., 'password' => ...]) を内部的に行うようになる
// class Member extends Authenticatable implements MustVerifyEmail
// {
//     public function getAuthIdentifierName()
//     {
//         return 'loginid';
//     }
// }
