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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique(); //クーポンコード
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('main_image')->nullable(); // メイン画像
            $table->boolean('is_active')->default(true); // 公開・非公開
            $table->date('expires_at')->nullable(); //利用期限
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
