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
        Schema::dropIfExists('icon_casts');
        Schema::dropIfExists('custom_table');
        Schema::dropIfExists('outsourcings');
    }

    public function down(): void
    {
        Schema::create('icon_casts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('filename');
            $table->timestamps();
        });

        Schema::create('custom_table', function (Blueprint $table) {
            $table->id();
            $table->integer('cast_id');
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->integer('ts_start');
            $table->integer('ts_end');
            $table->integer('sort');
            $table->timestamps();
        });

        Schema::create('outsourcings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->text('contact_info')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->text('invoice_info')->nullable();
            $table->text('memo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('sort')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }
};
