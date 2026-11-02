<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyoptionCate extends Model
{
    use HasFactory;
    protected $guarded = [

    ];

    public function myoptions() {
        return $this->hasMany(Myoption::class, 'myoption_cate_id');
    }
}
