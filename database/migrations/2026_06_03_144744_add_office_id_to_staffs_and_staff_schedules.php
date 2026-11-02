<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staffs', function (Blueprint $table) {
            if (!Schema::hasColumn('staffs', 'office_id')) {
                $table->unsignedBigInteger('office_id')->nullable()->after('id');
                $table->foreign('office_id')->references('id')->on('offices')->nullOnDelete();
            }
        });

        Schema::table('staff_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_schedules', 'office_id')) {
                $table->unsignedBigInteger('office_id')->nullable()->after('staff_id');
                $table->foreign('office_id')->references('id')->on('offices')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('staff_schedules', 'equipment_cate_id')) {
            $fks = array_column(DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'staff_schedules'
                AND TABLE_SCHEMA = DATABASE()
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            "), 'CONSTRAINT_NAME');

            $indexes = array_unique(array_column(DB::select('SHOW INDEX FROM staff_schedules'), 'Key_name'));

            // staff_schedules_unique が staff_id FK のバッキングインデックスになっているため先に削除
            if (in_array('staff_schedules_staff_id_foreign', $fks)) {
                Schema::table('staff_schedules', fn (Blueprint $t) => $t->dropForeign(['staff_id']));
            }

            if (in_array('staff_schedules_unique', $indexes)) {
                Schema::table('staff_schedules', fn (Blueprint $t) => $t->dropUnique('staff_schedules_unique'));
            }

            if (in_array('staff_schedules_equipment_cate_id_foreign', $fks)) {
                Schema::table('staff_schedules', fn (Blueprint $t) => $t->dropForeign(['equipment_cate_id']));
            }

            // FK削除後もインデックスが残る場合がある
            $indexes = array_unique(array_column(DB::select('SHOW INDEX FROM staff_schedules'), 'Key_name'));
            if (in_array('staff_schedules_equipment_cate_id_foreign', $indexes)) {
                Schema::table('staff_schedules', fn (Blueprint $t) => $t->dropIndex('staff_schedules_equipment_cate_id_foreign'));
            }

            Schema::table('staff_schedules', fn (Blueprint $t) => $t->dropColumn('equipment_cate_id'));

            // staff_id FK が失われていれば再追加
            $fks = array_column(DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'staff_schedules'
                AND TABLE_SCHEMA = DATABASE()
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            "), 'CONSTRAINT_NAME');

            if (!in_array('staff_schedules_staff_id_foreign', $fks)) {
                Schema::table('staff_schedules', fn (Blueprint $t) => $t->foreign('staff_id')->references('id')->on('staffs')->onDelete('cascade'));
            }
        }
    }

    public function down(): void
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn('office_id');
        });

        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn('office_id');
            $table->unsignedBigInteger('equipment_cate_id')->nullable();
        });
    }
};
