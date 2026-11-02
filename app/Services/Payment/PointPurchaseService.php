<?php

namespace App\Services\Payment;

use App\Models\Member;
use App\Models\MemberPoint;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ポイント購入サービス（Payment Links 版）
 *
 * Square Payment Links API でホスト型チェックアウトを開始し、
 * Webhook受信後に members.point を加算する。
 */
class PointPurchaseService
{
    public function __construct(
        private readonly SquarePaymentGatewayInterface $paymentGateway
    ) {
    }

    /**
     * ポイント購入チェックアウトを開始する。
     *
     * 1. payment_logs に pending レコードを作成
     * 2. Square Payment Links API でチェックアウトリンクを生成
     * 3. order_id を payload に保存（Webhook照合用）
     * 4. Dummy モードは status=completed なので即時 applyPoints
     * 5. 本番/Sandbox は checkout_url を返し、コントローラでリダイレクト
     *
     * @return array{ checkout_url: string|null, payment_log: PaymentLog }
     * @throws \RuntimeException
     */
    public function initiateCheckout(
        Member $member,
        int    $amountYen,
        int    $points,
        string $redirectUrl
    ): array {
        if ($amountYen <= 0 || $points <= 0) {
            throw new \InvalidArgumentException('Invalid amount or points.');
        }

        $paymentLog = PaymentLog::create([
            'member_id'     => $member->id,
            'fanclub_id'    => null,
            'amount'        => $amountYen,
            'purchase_type' => 'point_purchase',
            'status'        => 'pending',
            'points'        => $points,
        ]);

        $idempotencyKey = (string) Str::uuid();

        try {
            $result = $this->paymentGateway->createCheckoutLink(
                amountYen:      $amountYen,
                itemName:       'ポイント購入：' . number_format($points) . 'pt',
                idempotencyKey: $idempotencyKey,
                redirectUrl:    $redirectUrl,
                metadata: [
                    'reference_id' => 'log:' . $paymentLog->id,
                    'buyer_email'  => $member->email ?? '',
                    'member_id'    => (string) $member->id,
                    'purchase_type' => 'point_purchase',
                    'points'       => (string) $points,
                    'log_id'       => (string) $paymentLog->id,
                ]
            );
        } catch (\Throwable $e) {
            $paymentLog->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Point purchase checkout link creation failed', [
                'member_id' => $member->id,
                'amount'    => $amountYen,
                'points'    => $points,
                'log_id'    => $paymentLog->id,
                'error'     => $e->getMessage(),
            ]);

            throw $e;
        }

        $paymentLog->update([
            'payload' => array_merge((array) ($result['raw'] ?? []), [
                '_order_id' => $result['order_id'],
            ]),
        ]);

        // Dummy モード: status=completed → 即時ポイント付与
        if (strtolower($result['status']) === 'completed') {
            $this->applyPoints($paymentLog);
        }

        return [
            'checkout_url' => $result['checkout_url'],
            'payment_log'  => $paymentLog->fresh(),
        ];
    }

    /**
     * ポイントを加算する（Webhook または即時処理から呼ばれる）
     *
     * 二重加算防止: processed_at が null の場合のみ処理する。
     */
    public function applyPoints(PaymentLog $paymentLog): void
    {
        if ($paymentLog->isProcessed()) {
            return;
        }

        DB::transaction(function () use ($paymentLog) {
            $log = PaymentLog::lockForUpdate()->find($paymentLog->id);
            if ($log === null || $log->isProcessed()) {
                return;
            }

            $member = Member::lockForUpdate()->find($log->member_id);
            if ($member === null) {
                return;
            }

            $points = (int) $log->points;

            $member->increment('point', $points);

            MemberPoint::create([
                'member_id'      => $member->id,
                'point'          => $points,
                'status'         => 'add',
                'type'           => 'purchase',
                'payment_log_id' => $log->id,
                'memo'           => 'ポイント購入（' . number_format($log->amount) . '円）',
            ]);

            $log->update([
                'status'       => 'paid',
                'processed_at' => now(),
            ]);
        });
    }
}
