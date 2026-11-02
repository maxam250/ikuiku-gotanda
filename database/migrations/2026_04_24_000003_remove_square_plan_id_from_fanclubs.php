<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscription API廃止に伴い fanclubs.square_plan_id を削除する。
 * 単発決済では Square Plan は不要。
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fanclubs', function (Blueprint $table) {
            $table->dropColumn('square_plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('fanclubs', function (Blueprint $table) {
            $table->string('square_plan_id')->nullable()->after('status');
        });
    }
};
