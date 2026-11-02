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
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->text('email'); // 暗号化されたメールアドレス
            $table->string('email_hash', 64)->unique()->index(); // 検索用ハッシュ（SHA256）
            $table->string('token', 64)->unique()->index(); // 確認・解除用トークン
            $table->timestamp('verified_at')->nullable(); // 確認済み日時
            $table->timestamp('unsubscribed_at')->nullable(); // 解除日時
            $table->string('ip_address', 45)->nullable(); // 登録時のIPアドレス
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
