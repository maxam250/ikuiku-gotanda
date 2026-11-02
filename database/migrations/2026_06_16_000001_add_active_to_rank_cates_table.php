<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rank_cates', function (Blueprint $table) {
            $table->integer('active')->default(1)->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('rank_cates', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
