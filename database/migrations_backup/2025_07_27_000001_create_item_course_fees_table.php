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
        Schema::create('item_course_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('course_id');
            $table->integer('fee_60')->nullable()->comment('60分料金');
            $table->integer('fee_90')->nullable()->comment('90分料金');
            $table->integer('fee_120')->nullable()->comment('120分料金');
            $table->integer('fee_150')->nullable()->comment('150分料金');
            $table->integer('fee_180')->nullable()->comment('180分料金');
            $table->integer('fee_extension')->nullable()->comment('延長30分料金');
            $table->integer('uniform_rate')->nullable()->comment('一律料金（30分単位）');
            $table->boolean('use_uniform_rate')->default(false)->comment('一律料金を使用するかどうか');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            
            // 同じitem_idとcourse_idの組み合わせが重複しないようにする
            $table->unique(['item_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_course_fees');
    }
}; 