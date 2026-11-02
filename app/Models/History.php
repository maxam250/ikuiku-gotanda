<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'reservation_id', 'memo', 'flag'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    
}
