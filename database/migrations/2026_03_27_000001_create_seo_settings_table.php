<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();       // ルート名 (例: index, item.index)
            $table->string('page_name');                // 管理画面表示名 (例: トップページ)
            $table->string('meta_title')->nullable();   // <title>タグ
            $table->text('meta_description')->nullable(); // meta description
            $table->string('meta_keywords')->nullable(); // meta keywords
            $table->string('og_title')->nullable();     // OGP タイトル
            $table->text('og_description')->nullable(); // OGP 説明文
            $table->string('og_image')->nullable();     // OGP 画像URL
            $table->boolean('noindex')->default(false); // noindex設定
            $table->string('canonical_url')->nullable(); // canonical URL (空の場合は自動)
            $table->timestamps();
        });

        // デフォルトのSEO設定を挿入
        $now = now();
        DB::table('seo_settings')->insert([
            ['page_key' => 'index',             'page_name' => 'トップページ',       'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'item.index',         'page_name' => 'キャスト一覧',       'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'items.show',         'page_name' => 'キャスト詳細',       'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'schedule',           'page_name' => 'スケジュール',       'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'rank.index',         'page_name' => 'ランキング',         'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'costumes.index',     'page_name' => 'コスチューム',       'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'systems',            'page_name' => 'ご利用案内',         'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'studios',            'page_name' => 'スタジオ',           'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'access',             'page_name' => 'アクセス',           'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'contact',            'page_name' => 'お問い合わせ',       'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'events.index',       'page_name' => 'イベント',           'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'recruits.index',     'page_name' => 'キャスト募集',       'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'reservation.index',  'page_name' => 'ご予約',             'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'gallery.index',      'page_name' => 'ギャラリー',         'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'gallery.show',       'page_name' => 'ギャラリー詳細',     'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'news.index',         'page_name' => 'インフォメーション一覧', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'news.show',          'page_name' => 'インフォメーション詳細', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'blogs.index',        'page_name' => 'ブログ一覧',         'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'blog.show',          'page_name' => 'ブログ詳細',         'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'coupons.index',      'page_name' => 'クーポン',           'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'privacy',            'page_name' => 'プライバシーポリシー', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
