<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Castimg extends Model
{
    use HasFactory;

    protected $table = 'cast_img'; // テーブル名を指定

    protected $fillable = ['cast_id', 'filename', 'flag'];

    public function cast (){
        return $this->belongsTo(Cast::class);
    }

    public function schedules (){
        return $this->belongsTo(Schedule::class);
    }
}
