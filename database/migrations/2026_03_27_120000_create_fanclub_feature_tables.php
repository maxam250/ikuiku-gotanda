<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fanclubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cast_id')->constrained('items')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->string('status', 20)->default('active'); // active / paused / canceled
            $table->string('square_plan_id')->nullable(); // Square連携の将来拡張用
            $table->timestamps();

            $table->unique('cast_id');
            $table->index('status');
        });

        Schema::create('fanclub_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fanclub_id')->constrained('fanclubs')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('status', 20)->default('active'); // active / paused / canceled
            $table->string('payment_method', 30)->default('manual'); // manual / card / bank_transfer / points
            $table->string('payment_status', 30)->default('pending'); // pending / paid / unpaid / refunded
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('last_paid_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->string('square_customer_id')->nullable(); // Square連携の将来拡張用
            $table->string('square_subscription_id')->nullable(); // Square連携の将来拡張用
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->unique(['fanclub_id', 'member_id']);
            $table->index(['fanclub_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->index('payment_status');
        });

        Schema::create('fanclub_contents_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cast_id')->constrained('items')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['cast_id', 'sort']);
        });

        Schema::create('fanclub_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cast_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('fanclub_contents_categories')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('next_release_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['cast_id', 'published_at']);
            $table->index(['cast_id', 'scheduled_at']);
            $table->index(['cast_id', 'is_paid']);
        });

        Schema::create('fanclub_content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('fanclub_contents')->cascadeOnDelete();
            $table->string('type', 20); // image / video / text / link / timer
            $table->json('value'); // 種別ごとのデータ
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['content_id', 'sort']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fanclub_content_items');
        Schema::dropIfExists('fanclub_contents');
        Schema::dropIfExists('fanclub_contents_categories');
        Schema::dropIfExists('fanclub_memberships');
        Schema::dropIfExists('fanclubs');
    }
};

