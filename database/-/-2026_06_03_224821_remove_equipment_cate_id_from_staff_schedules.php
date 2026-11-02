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
        Schema::table('staff_schedules', function (Blueprint $table) {
            // カラムが存在する場合のみ削除
            if (Schema::hasColumn('staff_schedules', 'equipment_cate_id')) {

                // 外部キーが存在する場合のみ削除
                try {
                    $table->dropForeign(['equipment_cate_id']);
                } catch (\Throwable $e) {
                    // 外部キーが無い場合は無視
                }

                // カラム削除
                try {
                    $table->dropColumn('equipment_cate_id');
                } catch (\Throwable $e) {
                    // カラムが無い場合は無視
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_schedules', 'equipment_cate_id')) {
                $table->unsignedBigInteger('equipment_cate_id')->nullable();
                $table->foreign('equipment_cate_id')
                      ->references('id')
                      ->on('equipment_cates')
                      ->onDelete('set null');
            }
        });
    }
};
