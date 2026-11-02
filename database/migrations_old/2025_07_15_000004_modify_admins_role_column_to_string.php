<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // roleカラムをintegerからstringに変更
        Schema::table('admins', function (Blueprint $table) {
            $table->string('role')->default('admin')->change();
        });

        // 既存のデータを更新（1 = super_admin, その他 = admin）
        DB::table('admins')->where('role', 1)->update(['role' => 'super_admin']);
        DB::table('admins')->where('role', '!=', 'super_admin')->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // データをintegerに戻す
        DB::table('admins')->where('role', 'super_admin')->update(['role' => 1]);
        DB::table('admins')->where('role', 'admin')->update(['role' => 2]);

        // roleカラムをstringからintegerに変更
        Schema::table('admins', function (Blueprint $table) {
            $table->integer('role')->default(1)->change();
        });
    }
};
