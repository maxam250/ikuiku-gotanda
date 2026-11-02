<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * キャスト個別の外部ウィジェット（旧 item_data の item_shame_wiget / item_review_wiget / item_sch_wiget）
 * プロフィールページの写メ日記・口コミ・出勤ウィジェットに使用
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->text('item_shame_wiget')->nullable()->after('item_url');
            $table->text('item_review_wiget')->nullable()->after('item_shame_wiget');
            $table->text('item_sch_wiget')->nullable()->after('item_review_wiget');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['item_shame_wiget', 'item_review_wiget', 'item_sch_wiget']);
        });
    }
};
