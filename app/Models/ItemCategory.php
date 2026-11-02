<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemCategory extends Model
{
    use HasFactory;

    protected $table = 'item_categories';

    protected $fillable = ['name', 'icon_image', 'color', 'sort'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'item_category_id');
    }
}
