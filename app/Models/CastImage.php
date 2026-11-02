<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastImage extends Model
{
    use HasFactory;

    protected $table = 'cast_images'; // テーブル名を指定

    protected $fillable = ['cast_id', 'filename', 'flag', 'sort'];
    // protected $guarded = [

    // ];

    public function cast (){
        return $this->belongsTo(Cast::class);
    }


}
