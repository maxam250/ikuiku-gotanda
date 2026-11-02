<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('payment_description')->nullable()->after('fanclub_join_terms');
            $table->text('specified_commercial_transactions')->nullable()->after('payment_description');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['payment_description', 'specified_commercial_transactions']);
        });
    }
};
