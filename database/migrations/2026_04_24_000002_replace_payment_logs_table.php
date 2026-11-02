<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscription API廃止に伴い payment_logs テーブルを再設計する。
 *
 * 旧設計: Subscription/Invoice中心（fanclub_membership_id必須、subscription/invoice ID保持）
 * 新設計: 単発決済＋ポイント購入の両対応（fanclub_id nullable、purchase_type で分類）
 *
 * 変更点:
 *  - fanclub_membership_id (FK) を削除 → fanclub_id (nullable) で直接管理
 *  - square_invoice_id, square_subscription_id を削除
 *  - square_payment_id に UNIQUE制約付与（Webhook二重処理防止）
 *  - purchase_type カラム追加 (fanclub_join | point_purchase)
 *  - points カラム追加 (ポイント購入時の付与ポイント数)
 *  - processed_at カラム追加 (Webhook処理完了日時、二重防止の判定基準)
 *  - raw_response → payload にリネーム
 *  - status に 'pending' を追加 (決済前の状態を追跡)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payment_logs');

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('fanclub_id')->nullable()->constrained('fanclubs')->nullOnDelete();
            $table->string('square_payment_id')->nullable()->unique();
            $table->unsignedInteger('amount')->default(0);          // 金額（円）
            $table->string('purchase_type', 30);                    // fanclub_join | point_purchase
            $table->string('status', 20)->default('pending');       // pending | paid | failed | refunded
            $table->unsignedInteger('points')->nullable();          // ポイント購入時の付与ポイント数
            $table->json('payload')->nullable();                    // Square APIレスポンス原本
            $table->timestamp('processed_at')->nullable();          // Webhook処理完了日時（null = 未処理）
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index(['member_id', 'purchase_type']);
            $table->index('purchase_type');
            $table->index('status');
            $table->index('processed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fanclub_membership_id')->constrained('fanclub_memberships')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('fanclub_id')->constrained('fanclubs')->cascadeOnDelete();
            $table->string('square_invoice_id')->nullable();
            $table->string('square_payment_id')->nullable();
            $table->string('square_subscription_id')->nullable();
            $table->string('status', 30);
            $table->unsignedInteger('amount')->default(0);
            $table->string('currency', 10)->default('JPY');
            $table->json('raw_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['fanclub_membership_id', 'created_at']);
            $table->index(['member_id', 'status']);
            $table->index('square_invoice_id');
            $table->index('square_payment_id');
            $table->index('status');
        });
    }
};
