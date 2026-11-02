<?php

namespace App\Services\Payment;

use App\Models\Member;
use Illuminate\Support\Str;

/**
 * 開発・テスト用のダミー決済ゲートウェイ。
 * Square API への実際の通信は行わない。
 * FANCLUB_PAYMENT_DRIVER=dummy_square の時に使用される。
 */
class DummySquarePaymentGateway implements SquarePaymentGatewayInterface
{
    public function createOrGetCustomer(Member $member): string
    {
        return 'dummy_customer_' . $member->id;
    }

    public function createPayment(
        int    $amountYen,
        string $sourceId,
        string $idempotencyKey,
        array  $metadata = []
    ): array {
        $paymentId = 'dummy_payment_' . Str::uuid();

        return [
            'payment_id' => $paymentId,
            'status'     => 'completed',
            'amount'     => $amountYen,
            'raw'        => [
                'payment' => [
                    'id'           => $paymentId,
                    'status'       => 'COMPLETED',
                    'amount_money' => ['amount' => $amountYen, 'currency' => 'JPY'],
                ],
            ],
        ];
    }

    public function createCheckoutLink(
        int    $amountYen,
        string $itemName,
        string $idempotencyKey,
        string $redirectUrl,
        array  $metadata = []
    ): array {
        return [
            'checkout_url' => null,   // null = サーバー側で直接完了処理
            'order_id'     => 'dummy_order_' . Str::uuid(),
            'status'       => 'completed',
            'raw'          => [],
        ];
    }
}
