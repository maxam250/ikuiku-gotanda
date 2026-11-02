<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffSchedule extends Model
{
    use HasFactory;

    protected $table = 'staff_schedules';

    protected $fillable = [
        'staff_id',
        'office_id',
        'date',
        'memo',
        'schedule_type',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
