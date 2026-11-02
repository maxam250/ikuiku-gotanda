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
        Schema::create('shared_memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('shared_memo_categories')->nullOnDelete();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->foreign('staff_id')->references('id')->on('staffs')->nullOnDelete();
            $table->date('memo_date');
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_memos');
    }
};
