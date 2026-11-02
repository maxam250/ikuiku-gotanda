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
        Schema::rename('studio_sub_images', 'equipment_sub_images');
    }

    public function down(): void
    {
        Schema::rename('equipment_sub_images', 'studio_sub_images');
    }
};
