<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'page_key',
        'page_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'noindex',
        'canonical_url',
        'breadcrumb_label',
        'breadcrumb_parent_key',
    ];

    protected $casts = [
        'noindex' => 'boolean',
    ];

    /**
     * ルート名からSEO設定を取得する
     */
    public static function findByRouteKey(string $routeKey): ?self
    {
        return static::where('page_key', $routeKey)->first();
    }
}
