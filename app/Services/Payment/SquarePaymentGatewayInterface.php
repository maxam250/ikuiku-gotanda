<?php

namespace App\Services\Payment;

use App\Models\Member;

/**
 * Square 単発決済（Payments API）インターフェース
 *
 * Subscription API は使用しない。
 * 単発の createPayment のみを提供する。
 */
interface SquarePaymentGatewayInterface
{
    /**
     * Square Customer を取得または作成し、customer_id を返す。
     */
    public function createOrGetCustomer(Member $member): string;

    /**
     * Square Payments API で単発決済を実行する。
     *
     * @return array{ payment_id: string, status: string, amount: int, raw: array }
     * @throws \RuntimeException 決済失敗時
     */
    public function createPayment(
        int    $amountYen,
        string $sourceId,
        string $idempotencyKey,
        array  $metadata = []
    ): array;

    /**
     * Square Payment Links API でホスト型チェックアウトリンクを作成する。
     *
     * @param int    $amountYen      決済金額（円）
     * @param string $itemName       商品名（ファンクラブ名等）
     * @param string $idempotencyKey 冪等キー（UUIDを推奨）
     * @param string $redirectUrl    支払い完了後のリダイレクト先URL
     * @param array  $metadata       付加情報（log_id, member_id 等）
     *
     * @return array{ checkout_url: string|null, order_id: string, status: string, raw: array }
     * @throws \RuntimeException 作成失敗時
     */
    public function createCheckoutLink(
        int    $amountYen,
        string $itemName,
        string $idempotencyKey,
        string $redirectUrl,
        array  $metadata = []
    ): array;
}
