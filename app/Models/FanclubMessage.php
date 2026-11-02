<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FanclubMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fanclub_id',
        'member_id',
        'parent_id',
        'sender_type',
        'message',
        'image_path',
        'is_read',
        'read_at',
        'is_warned',
        'is_hidden',
        'admin_note',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'is_warned'  => 'boolean',
        'is_hidden'  => 'boolean',
        'read_at'    => 'datetime',
    ];

    public function fanclub(): BelongsTo
    {
        return $this->belongsTo(Fanclub::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FanclubMessage::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FanclubMessage::class, 'parent_id')->orderBy('created_at');
    }

    /** 禁止ワードチェック */
    public static function containsBannedWord(string $text): bool
    {
        $words = FanclubBannedWord::pluck('word');
        foreach ($words as $word) {
            if (mb_stripos($text, $word) !== false) {
                return true;
            }
        }
        return false;
    }
}
