<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class GuestMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'sender_type',
        'message',
        'title',
        'image_path',
        'is_read',
        'read_at',
        'reservation_id',
        'mailgun_message_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        // 'email' => 'encrypted', // 手動で暗号化するため削除
    ];

    /**
     * 関連する予約情報
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * 暗号化されたメールアドレスで検索
     */
    public static function findByEmail($email)
    {
        // 暗号化されたメールアドレスで直接検索
        $encryptedEmail = Crypt::encryptString($email);
        return static::where('email', $encryptedEmail)->get();
    }

    /**
     * 復号化されたメールアドレスを取得
     */
    public function getDecryptedEmailAttribute()
    {
        try {
            return Crypt::decryptString($this->getRawOriginal('email'));
        } catch (\Exception $e) {
            return $this->getRawOriginal('email');
        }
    }
}
