<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = [
        'name',
        'memo',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }

    public function staffSchedules()
    {
        return $this->hasMany(StaffSchedule::class);
    }
}
