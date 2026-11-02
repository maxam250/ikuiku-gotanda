<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryListImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'filename',
        'name',
        'description',
        'sort',
    ];

    /**
     * Equipmentとのリレーション
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
} 