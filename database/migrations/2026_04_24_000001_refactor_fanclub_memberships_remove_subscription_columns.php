<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscription API廃止に伴い、fanclub_memberships から
 * 定期課金専用カラムを削除する。
 * 単発決済への移行後は payment_logs で決済情報を管理する。
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fanclub_memberships', function (Blueprint $table) {
            // インデックス削除（先にインデックスを落とす）
            $table->dropIndex(['payment_status']);

            // Subscription固有カラムを削除
            $table->dropColumn([
                'payment_status',        // pending/paid/unpaid (Subscription課金ステータス)
                'next_billing_at',       // 次回課金日 (月額前提)
                'square_subscription_id', // Square Subscription ID
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('fanclub_memberships', function (Blueprint $table) {
            $table->string('payment_status', 30)->default('pending')->after('payment_method');
            $table->timestamp('next_billing_at')->nullable()->after('ended_at');
            $table->string('square_subscription_id')->nullable()->after('square_customer_id');
            $table->index('payment_status');
        });
    }
};
