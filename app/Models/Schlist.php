<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schlist extends Model
{
    use HasFactory;


    // public function cast()
    // {
    //     return $this->belongsTo(Cast::class, 'cast_id');
    // }

    public function cast()
    {
        return $this->belongsTo(Cast::class);
    }

}
