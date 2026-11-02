<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fanclub_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fanclub_id')->constrained('fanclubs')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('fanclub_messages')->nullOnDelete();
            $table->enum('sender_type', ['member', 'cast']);
            $table->text('message');
            $table->string('image_path')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_warned')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fanclub_id', 'member_id']);
        });

        Schema::create('fanclub_banned_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fanclub_messages');
        Schema::dropIfExists('fanclub_banned_words');
    }
};
