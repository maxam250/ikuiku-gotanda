<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_uuid',
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'message',
        'title',
        'image_path',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }
} 