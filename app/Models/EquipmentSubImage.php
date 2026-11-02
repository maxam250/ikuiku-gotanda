<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentSubImage extends Model
{
    use HasFactory;

    protected $table = 'equipment_sub_images';

    protected $fillable = [
        'equipment_id',
        'filename',
        'name',
        'description',
        'sort',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
