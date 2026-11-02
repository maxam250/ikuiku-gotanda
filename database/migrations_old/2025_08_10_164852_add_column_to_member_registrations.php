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
        // 1. 先に不要なカラムを削除
        Schema::table('member_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('member_registrations', 'member_id')) {
                $table->dropColumn('member_id');
            }
            if (Schema::hasColumn('member_registrations', 'token')) {
                $table->dropColumn('token');
            }
        });

        // 2. 新しいカラムを追加
        Schema::table('member_registrations', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('loginid')->after('name');
            $table->unique('loginid', 'member_registrations_loginid_unique');
            $table->string('email')->after('loginid');
            $table->string('password')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. インデックスが存在する場合だけ削除
        Schema::table('member_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('member_registrations', 'loginid')) {
                $table->dropUnique('member_registrations_loginid_unique');
                $table->dropColumn('loginid');
            }

            if (Schema::hasColumn('member_registrations', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('member_registrations', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('member_registrations', 'password')) {
                $table->dropColumn('password');
            }
        });

        // 2. 削除したカラムを復元
        Schema::table('member_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->after('id');
            $table->string('token')->after('member_id');
        });
    }
};
