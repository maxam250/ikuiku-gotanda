<?php

namespace App\Services\Payment;

use App\Models\Member;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Square Payments API（単発決済）実装
 *
 * Subscription API は使用しない。
 * POST /v2/payments で単発の支払いを作成する。
 */
class SquarePaymentGateway implements SquarePaymentGatewayInterface
{
    private function baseUrl(): string
    {
        return config('fanclub.payments.square.environment') === 'production'
            ? 'https://connect.squareup.com'
            : 'https://connect.squareupsandbox.com';
    }

    private function request()
    {
        $token = (string) config('fanclub.payments.square.access_token');
        if ($token === '') {
            throw new RuntimeException('Square access token is not configured.');
        }

        return Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Square-Version' => '2025-10-16',
            ]);
    }

    public function createOrGetCustomer(Member $member): string
    {
        if (!empty($member->square_customer_id)) {
            return (string) $member->square_customer_id;
        }

        $response = $this->request()->post($this->baseUrl() . '/v2/customers', [
            'idempotency_key' => (string) Str::uuid(),
            'given_name'      => $member->name ?? 'Member',
            'email_address'   => $member->email ?? null,
            'reference_id'    => 'member:' . $member->id,
        ])->throw()->json();

        $customerId = data_get($response, 'customer.id');
        if (!$customerId) {
            throw new RuntimeException('Square customer creation failed: ' . json_encode($response));
        }

        $member->square_customer_id = $customerId;
        $member->save();

        return $customerId;
    }

    /**
     * Square Payments API で単発決済を実行する。
     *
     * Square の金額単位は「最小通貨単位」なので、
     * JPY（ゼロ小数点通貨）は円 = Square amount がそのまま一致する。
     */
    public function createPayment(
        int    $amountYen,
        string $sourceId,
        string $idempotencyKey,
        array  $metadata = []
    ): array {
        $locationId = config('fanclub.payments.square.location_id');
        if (empty($locationId)) {
            throw new RuntimeException('Square location_id is not configured.');
        }

        $body = [
            'idempotency_key' => $idempotencyKey,
            'source_id'       => $sourceId,
            'amount_money'    => [
                'amount'   => $amountYen,   // JPY はセントなし
                'currency' => 'JPY',
            ],
            'location_id'     => $locationId,
            'autocomplete'    => true,      // 即時 COMPLETED に遷移
        ];

        // メタデータをセット（member_id, fanclub_id, purchase_type, log_id 等）
        if (!empty($metadata)) {
            // Square metadata は string => string のみ
            $body['note']        = data_get($metadata, 'note', '');
            $body['reference_id'] = (string) data_get($metadata, 'reference_id', '');

            // Square metadata フィールド（値はすべて文字列）
            $squareMeta = [];
            foreach ($metadata as $k => $v) {
                if (in_array($k, ['note', 'reference_id'], true)) {
                    continue;
                }
                $squareMeta[(string) $k] = (string) $v;
            }
            if (!empty($squareMeta)) {
                $body['metadata'] = $squareMeta;
            }
        }

        $response = $this->request()
            ->post($this->baseUrl() . '/v2/payments', $body)
            ->throw()
            ->json();

        $payment = data_get($response, 'payment');
        if (!$payment) {
            throw new RuntimeException('Square payment creation failed: ' . json_encode($response));
        }

        $paymentId = data_get($payment, 'id');
        $status    = strtolower((string) data_get($payment, 'status', 'unknown'));
        $amount    = (int) data_get($payment, 'amount_money.amount', 0);

        if (!$paymentId) {
            throw new RuntimeException('Square payment ID missing: ' . json_encode($response));
        }

        Log::info('Square payment created', [
            'payment_id' => $paymentId,
            'status'     => $status,
            'amount'     => $amount,
        ]);

        return [
            'payment_id' => $paymentId,
            'status'     => $status,
            'amount'     => $amount,
            'raw'        => $response,
        ];
    }

    /**
     * Square Payment Links API でホスト型チェックアウトリンクを作成する。
     * POST /v2/online-checkout/payment-links
     */
    public function createCheckoutLink(
        int    $amountYen,
        string $itemName,
        string $idempotencyKey,
        string $redirectUrl,
        array  $metadata = []
    ): array {
        $locationId = config('fanclub.payments.square.location_id');
        if (empty($locationId)) {
            throw new RuntimeException('Square location_id is not configured.');
        }

        $body = [
            'idempotency_key' => $idempotencyKey,
            'order' => [
                'location_id'  => $locationId,
                'reference_id' => (string) data_get($metadata, 'reference_id', ''),
                'line_items'   => [
                    [
                        'name'             => $itemName,
                        'quantity'         => '1',
                        'base_price_money' => [
                            'amount'   => $amountYen,
                            'currency' => 'JPY',
                        ],
                    ],
                ],
            ],
            'checkout_options' => [
                'redirect_url'             => $redirectUrl,
                'ask_for_shipping_address' => false,
            ],
        ];

        $buyerEmail = data_get($metadata, 'buyer_email');
        if ($buyerEmail && filter_var($buyerEmail, FILTER_VALIDATE_EMAIL)) {
            $body['pre_populated_data'] = ['buyer_email' => $buyerEmail];
        }

        $response = $this->request()
            ->post($this->baseUrl() . '/v2/online-checkout/payment-links', $body)
            ->throw()
            ->json();

        $link = data_get($response, 'payment_link');
        if (!$link) {
            throw new RuntimeException('Square checkout link creation failed: ' . json_encode($response));
        }

        $checkoutUrl = (string) data_get($link, 'url', '');
        $orderId     = (string) data_get($link, 'order_id', '');

        Log::info('Square checkout link created', [
            'order_id'    => $orderId,
            'checkout_url' => $checkoutUrl,
        ]);

        return [
            'checkout_url' => $checkoutUrl ?: null,
            'order_id'     => $orderId,
            'status'       => 'pending',
            'raw'          => $response,
        ];
    }
}
