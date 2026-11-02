<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE shops MODIFY COLUMN shop_map TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE shops MODIFY COLUMN shop_map VARCHAR(255) NULL');
    }
};
