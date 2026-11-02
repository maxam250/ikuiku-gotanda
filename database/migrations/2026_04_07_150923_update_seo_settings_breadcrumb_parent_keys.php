<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 各showページの breadcrumb_parent_key を設定
        $updates = [
            'news.show'          => 'news.index',
            'items.show'         => 'item.index',
            'gallery.show'       => 'gallery.index',
            'fanclub.show'       => 'fanclub',
        ];

        foreach ($updates as $pageKey => $parentKey) {
            DB::table('seo_settings')
                ->where('page_key', $pageKey)
                ->update(['breadcrumb_parent_key' => $parentKey]);
        }

        // blog.show は実際のルート名が blogs.show なので page_key を修正して parent も設定
        DB::table('seo_settings')
            ->where('page_key', 'blog.show')
            ->update([
                'page_key'              => 'blogs.show',
                'breadcrumb_parent_key' => 'blogs.index',
            ]);

        // coupons.show が未登録なら追加
        if (!DB::table('seo_settings')->where('page_key', 'coupons.show')->exists()) {
            DB::table('seo_settings')->insert([
                'page_key'              => 'coupons.show',
                'page_name'             => 'クーポン詳細',
                'breadcrumb_parent_key' => 'coupons.index',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
        }

        // specialEvents.show が未登録なら追加
        if (!DB::table('seo_settings')->where('page_key', 'specialEvents.show')->exists()) {
            DB::table('seo_settings')->insert([
                'page_key'              => 'specialEvents.show',
                'page_name'             => '特設イベント詳細',
                'breadcrumb_parent_key' => 'specialEvents.index',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
        }
    }

    public function down(): void
    {
        $revert = [
            'news.show'          => null,
            'items.show'         => null,
            'gallery.show'       => null,
            'fanclub.show'       => null,
            'blogs.show'         => null,
            'coupons.show'       => null,
            'specialEvents.show' => null,
        ];

        foreach ($revert as $pageKey => $parentKey) {
            DB::table('seo_settings')
                ->where('page_key', $pageKey)
                ->update(['breadcrumb_parent_key' => $parentKey]);
        }

        // blogs.show を blog.show に戻す
        DB::table('seo_settings')
            ->where('page_key', 'blogs.show')
            ->update(['page_key' => 'blog.show']);
    }
};
