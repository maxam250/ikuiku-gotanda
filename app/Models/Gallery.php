<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'sort',
        'active',
        'item_id',
        'member_id',
        'flag',
        'gallery_video',
    ];

    public function images()
    {
        // return $this->hasMany(GalleryImage::class);
        return $this->hasMany(GalleryImage::class)->orderBy('sort');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }


}
