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
        Schema::table('mycostumes', function (Blueprint $table) {
            $table->dropForeign(['mycostume_cate_id']);
            $table->unsignedBigInteger('mycostume_cate_id')->nullable()->change();
            $table->foreign('mycostume_cate_id')->references('id')->on('mycostume_cates')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('mycostumes', function (Blueprint $table) {
            $table->dropForeign(['mycostume_cate_id']);
            $table->unsignedBigInteger('mycostume_cate_id')->nullable(false)->change();
            $table->foreign('mycostume_cate_id')->references('id')->on('mycostume_cates')->onDelete('cascade');
        });
    }
};
