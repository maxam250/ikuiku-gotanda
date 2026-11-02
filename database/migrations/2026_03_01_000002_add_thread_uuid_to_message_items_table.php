<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('message_items', function (Blueprint $table) {
            // メール返信連携用のスレッド識別子（キャスト向け）。
            // 管理者がキャストへ送信した際に生成し、Reply-To アドレスに埋め込む。
            $table->string('thread_uuid', 36)->nullable()->index()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('message_items', function (Blueprint $table) {
            $table->dropIndex(['thread_uuid']);
            $table->dropColumn('thread_uuid');
        });
    }
};
