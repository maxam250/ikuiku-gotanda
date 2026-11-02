<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->string('breadcrumb_label', 100)->nullable()->after('canonical_url')
                  ->comment('パンくずリストに表示するラベル');
            $table->string('breadcrumb_parent_key', 100)->nullable()->after('breadcrumb_label')
                  ->comment('親ページの page_key（ルート名）');
        });
    }

    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn(['breadcrumb_label', 'breadcrumb_parent_key']);
        });
    }
};
