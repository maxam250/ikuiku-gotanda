<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fanclub_membership_id')->constrained('fanclub_memberships')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('fanclub_id')->constrained('fanclubs')->cascadeOnDelete();
            $table->string('square_invoice_id')->nullable();
            $table->string('square_payment_id')->nullable();
            $table->string('square_subscription_id')->nullable();
            $table->string('status', 30); // paid / failed / refunded
            $table->unsignedInteger('amount')->default(0);
            $table->string('currency', 10)->default('JPY');
            $table->json('raw_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['fanclub_membership_id', 'created_at']);
            $table->index(['member_id', 'status']);
            $table->index('square_invoice_id');
            $table->index('square_payment_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
