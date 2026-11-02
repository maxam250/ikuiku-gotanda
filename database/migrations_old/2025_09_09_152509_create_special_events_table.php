<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('special_events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // イベントタイトル

            $table->date('event_date'); // 開催日
            $table->integer('start_hour'); // 開始時間（時）
            $table->integer('start_minute'); // 開始時間（分）
            $table->integer('end_hour'); // 終了時間（時）
            $table->integer('end_minute'); // 終了時間（分）

            $table->text('description'); // 内容
            $table->json('item_ids')->nullable(); // item_id の配列（checkbox登録）

            $table->string('main_image')->nullable(); // メイン画像
            $table->integer('fee')->nullable();       // 料金（数値型）
            $table->string('fee_note')->nullable();   // 料金注意事項

            $table->string('venue')->nullable();      // 場所
            $table->text('access_info')->nullable();  // アクセス案内
            $table->string('map_url')->nullable();    // Google Map URL

            $table->boolean('is_active')->default(false); // 公開・非公開
            $table->string('external_link')->nullable();  // 外部リンク
            $table->text('notes')->nullable();            // 備考・注意事項

            $table->boolean('has_timer')->default(false); // タイマーフラグ
            $table->date('timer_start')->nullable();      // 表示開始日
            $table->date('timer_end')->nullable();        // 表示終了日

            $table->integer('sort_order')->default(0);    // ソート順

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_events');
    }
};
