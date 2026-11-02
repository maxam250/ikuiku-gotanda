<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'course',
        'course_time',
        'fee',
        'text',
        'sort',
    ];


    /**
     * 料金が属するコースとのリレーションを定義
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'id', 'course_id');
    }


}
