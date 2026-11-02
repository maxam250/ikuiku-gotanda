<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_groups', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('is_publish')->default(1);
            $table->string('name');
            $table->string('url')->nullable();
            $table->integer('sort')->default(0);
            $table->string('filename')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_groups');
    }
};
