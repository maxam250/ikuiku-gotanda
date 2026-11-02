<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MycostumeCate extends Model
{
    use HasFactory;

    protected $guarded = [

    ];

    public function mycostumes() {
        return $this->hasMany(Mycostume::class, 'mycostume_cate_id');
    }
}
