<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guest_messages', function (Blueprint $table) {
            $table->id();
            $table->text('email')->comment('暗号化されたメールアドレス');
            $table->string('sender_type')->comment('admin or guest');
            $table->text('message');
            $table->string('title')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable()->comment('関連する予約ID');
            $table->string('mailgun_message_id')->nullable()->comment('Mailgunメッセージ ID');
            $table->timestamps();
            $table->softDeletes();

            // インデックスは後で追加
            $table->index('reservation_id');
            $table->index('created_at');
            $table->index('mailgun_message_id');
        });

        // emailカラムのインデックスを手動で追加
        DB::statement('ALTER TABLE guest_messages ADD INDEX idx_email_prefix (email(100))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_messages');
    }
};
