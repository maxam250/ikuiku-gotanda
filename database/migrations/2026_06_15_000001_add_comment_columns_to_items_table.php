<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->text('item_comment_nominate')->nullable()->after('item_comment2');
            $table->text('item_comment_faq')->nullable()->after('item_comment_nominate');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['item_comment_nominate', 'item_comment_faq']);
        });
    }
};
