<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('equipment_cate_id')->nullable();
            $table->date('date');
            $table->string('schedule_type')->default('work'); // work / off
            $table->text('memo')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('cascade');
            $table->foreign('equipment_cate_id')->references('id')->on('equipment_cates')->onDelete('set null');

            $table->unique(['staff_id', 'equipment_cate_id', 'date'], 'staff_schedules_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_schedules');
    }
};
