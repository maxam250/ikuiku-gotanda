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
        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('newsletter_templates')->onDelete('set null');
            $table->string('subject'); // 件名
            $table->text('body_text')->nullable(); // プレーンテキスト本文
            $table->longText('body_html')->nullable(); // HTML本文
            $table->timestamp('scheduled_at')->nullable(); // 配信予定日時
            $table->timestamp('sent_at')->nullable(); // 配信完了日時
            $table->integer('total_recipients')->default(0); // 送信先数
            $table->integer('sent_count')->default(0); // 送信済み数
            $table->integer('failed_count')->default(0); // 失敗数
            $table->enum('status', ['draft', 'scheduled', 'sending', 'completed', 'failed', 'cancelled'])->default('draft');
            $table->text('filter_status')->nullable(); // フィルター条件（JSON形式）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_campaigns');
    }
};
