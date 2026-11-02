<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_points', function (Blueprint $table) {
            // type: purchase / manual_add / manual_del / use / expired / refund
            $table->string('type', 20)->default('manual_add')->after('status');

            // 決済ログへの参照（ポイント購入時）
            $table->unsignedBigInteger('payment_log_id')->nullable()->after('type');
            $table->foreign('payment_log_id')->references('id')->on('payment_logs')->nullOnDelete();

            // ポイント有効期限（将来的な期限付きポイントに対応）
            $table->timestamp('expired_at')->nullable()->after('payment_log_id');

            // 関連モデルへのポリモーフィック参照（サービス・商品購入での使用時）
            $table->string('related_type', 100)->nullable()->after('expired_at');
            $table->unsignedBigInteger('related_id')->nullable()->after('related_type');
        });

        // 既存レコードを status から type へバックフィル
        DB::statement("UPDATE member_points SET type = 'manual_add' WHERE status = 'add' AND type = 'manual_add'");
        DB::statement("UPDATE member_points SET type = 'manual_del' WHERE status = 'del'");
    }

    public function down(): void
    {
        Schema::table('member_points', function (Blueprint $table) {
            $table->dropForeign(['payment_log_id']);
            $table->dropColumn(['type', 'payment_log_id', 'expired_at', 'related_type', 'related_id']);
        });
    }
};
