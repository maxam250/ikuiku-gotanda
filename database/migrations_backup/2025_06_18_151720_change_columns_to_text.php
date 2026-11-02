<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // membersテーブル
        Schema::table('members', function (Blueprint $table) {
            $table->text('email')->change();
            $table->text('name')->change();
        });

        // reservationsテーブル
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('item_name_other')->nullable()->after('item_name');
            $table->text('email')->nullable()->change();
            $table->text('tel')->nullable()->change();
        });

        // itemsテーブル
        Schema::table('items', function (Blueprint $table) {
            $table->text('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        // 255文字を超えるデータを短縮
        \DB::statement("UPDATE members SET email = LEFT(email, 255) WHERE CHAR_LENGTH(email) > 255");
        \DB::statement("UPDATE members SET name = LEFT(name, 255) WHERE CHAR_LENGTH(name) > 255");
        \DB::statement("UPDATE reservations SET email = LEFT(email, 255) WHERE CHAR_LENGTH(email) > 255");
        \DB::statement("UPDATE reservations SET tel = LEFT(tel, 255) WHERE CHAR_LENGTH(tel) > 255");
        \DB::statement("UPDATE items SET email = LEFT(email, 255) WHERE CHAR_LENGTH(email) > 255");

        // その後で型を戻す
        Schema::table('members', function (Blueprint $table) {
            $table->string('email')->change();
            $table->string('name')->change();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('tel')->nullable()->change();
            $table->dropColumn('item_name_other');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }
};
