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
        Schema::table('equipments', function (Blueprint $table) {
            $table->string('filename_other')->nullable()->comment('見取り図用画像');
            $table->string('map_url')->nullable()->comment('アクセスマップのリンクURL');
            $table->text('map_code')->nullable()->comment('アクセスマップ（GoogleMap）のhtmlタグ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn(['filename_other', 'map_url', 'map_code']);
        });
    }
}; 