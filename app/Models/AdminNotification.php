<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'related_id',
        'title',
        'message',
        'sender',
        'color',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * 通知を作成
     */
    public static function createNotification($type, $relatedId, $title, $message, $sender, $color)
    {
        return self::create([
            'type' => $type,
            'related_id' => $relatedId,
            'title' => $title,
            'message' => $message,
            'sender' => $sender,
            'color' => $color,
        ]);
    }

    /**
     * 通知を既読にする
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * 関連するレコードが削除された時に通知を削除
     */
    public static function deleteByRelatedRecord($type, $relatedId)
    {
        return self::where('type', $type)
            ->where('related_id', $relatedId)
            ->delete();
    }
}
