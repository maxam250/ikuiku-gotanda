<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('fee')->nullable();
            $table->string('note')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('premium_rank_id')->nullable()->after('item_catch')
                  ->constrained('premium_ranks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['premium_rank_id']);
            $table->dropColumn('premium_rank_id');
        });

        Schema::dropIfExists('premium_ranks');
    }
};
