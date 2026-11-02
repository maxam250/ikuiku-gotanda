<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('square_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->nullable()->unique();
            $table->string('event_type', 100);
            $table->json('payload');
            $table->string('status', 30)->default('received'); // received / processed / failed / skipped
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('event_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('square_webhook_logs');
    }
};
