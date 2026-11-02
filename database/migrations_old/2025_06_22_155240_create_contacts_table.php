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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('お名前');
            $table->text('email')->comment('メールアドレス');
            $table->text('phone')->nullable()->comment('電話番号');
            $table->enum('inquiry_type', ['general', 'reservation', 'cast', 'other'])->comment('問い合わせ種別');
            $table->string('subject')->comment('件名');
            $table->text('message')->comment('問い合わせ内容');
            $table->enum('status', ['unread', 'in_progress', 'completed'])->default('unread')->comment('ステータス');
            $table->text('admin_memo')->nullable()->comment('管理者メモ');
            $table->string('ip_address')->nullable()->comment('IPアドレス（スパム対策）');
            $table->string('user_agent')->nullable()->comment('ユーザーエージェント（スパム対策）');
            $table->boolean('is_spam')->default(false)->comment('スパムフラグ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
