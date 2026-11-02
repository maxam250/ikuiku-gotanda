<?php

namespace App\Services\Fanclub;

use App\Mail\FanclubJoinedAdminMail;
use App\Mail\FanclubJoinedCastMail;
use App\Mail\FanclubPaymentSuccessMail;
use App\Models\Fanclub;
use App\Models\FanclubMembership;
use App\Models\Member;
use App\Models\PaymentLog;
use App\Services\Payment\SquarePaymentGatewayInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * ファンクラブ参加サービス（単発決済版）
 *
 * Subscription API は使用しない。
 * Square Payments API で単発決済を行い、
 * Webhook受信後に参加状態を反映する。
 */
class FanclubMembershipService
{
    public function __construct(
        private readonly SquarePaymentGatewayInterface $paymentGateway
    ) {
    }

    /**
     * ファンクラブ参加決済を開始する。
     *
     * 1. payment_logs に pending レコードを作成
     * 2. Square Payments API で決済実行
     * 3. 決済が即時 COMPLETED なら参加状態も即時反映
     * 4. PENDING/APPROVED の場合は Webhook を待つ
     *
     * @throws \RuntimeException 決済失敗時
     */
    public function initiateJoin(Fanclub $fanclub, Member $member, string $sourceId): PaymentLog
    {
        // 既に参加中の場合はエラー
        $existing = FanclubMembership::where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            throw new \RuntimeException('already_joined');
        }

        return DB::transaction(function () use ($fanclub, $member, $sourceId) {
            // 1. pending の payment_log を先に作成（参照IDとして使用）
            $paymentLog = PaymentLog::create([
                'member_id'     => $member->id,
                'fanclub_id'    => $fanclub->id,
                'amount'        => $fanclub->price,
                'purchase_type' => 'fanclub_join',
                'status'        => 'pending',
            ]);

            $idempotencyKey = (string) Str::uuid();

            try {
                $result = $this->paymentGateway->createPayment(
                    amountYen:      $fanclub->price,
                    sourceId:       $sourceId,
                    idempotencyKey: $idempotencyKey,
                    metadata: [
                        'reference_id'  => 'log:' . $paymentLog->id,
                        'note'          => 'ファンクラブ参加: ' . $fanclub->title,
                        'member_id'     => (string) $member->id,
                        'fanclub_id'    => (string) $fanclub->id,
                        'purchase_type' => 'fanclub_join',
                        'log_id'        => (string) $paymentLog->id,
                    ]
                );
            } catch (\Throwable $e) {
                // 決済失敗 → payment_log を failed に更新
                $paymentLog->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                Log::error('Fanclub join payment failed', [
                    'member_id'  => $member->id,
                    'fanclub_id' => $fanclub->id,
                    'log_id'     => $paymentLog->id,
                    'error'      => $e->getMessage(),
                ]);

                throw $e;
            }

            // payment_id を記録
            $paymentLog->update([
                'square_payment_id' => $result['payment_id'],
                'payload'           => $result['raw'],
            ]);

            // Square が即時 COMPLETED を返した場合は参加を即時反映
            if (strtolower($result['status']) === 'completed') {
                $this->applyFanclubJoin($paymentLog);
            }

            return $paymentLog->fresh();
        });
    }

    /**
     * 無料ファンクラブへ即時入会する（決済不要）。
     *
     * @throws \RuntimeException 既に入会済みの場合
     */
    public function joinFree(Fanclub $fanclub, Member $member): void
    {
        $existing = FanclubMembership::where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            throw new \RuntimeException('already_joined');
        }

        $paymentLog = PaymentLog::create([
            'member_id'     => $member->id,
            'fanclub_id'    => $fanclub->id,
            'amount'        => 0,
            'purchase_type' => 'fanclub_join',
            'status'        => 'pending',
        ]);

        $this->applyFanclubJoin($paymentLog);
    }

    /**
     * Square Payment Links でホスト型チェックアウトを開始する。
     *
     * 1. payment_logs に pending レコードを作成
     * 2. Square Payment Links API でチェックアウトリンクを生成
     * 3. order_id を payload に保存（Webhook照合用）
     * 4. Dummy モードは status=completed なので即時 applyFanclubJoin
     * 5. 本番/Sandbox は checkout_url を返し、コントローラでリダイレクト
     *
     * @return array{ checkout_url: string|null, payment_log: PaymentLog }
     * @throws \RuntimeException
     */
    public function initiateCheckout(Fanclub $fanclub, Member $member, string $redirectUrl): array
    {
        $existing = FanclubMembership::where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            throw new \RuntimeException('already_joined');
        }

        $paymentLog = PaymentLog::create([
            'member_id'     => $member->id,
            'fanclub_id'    => $fanclub->id,
            'amount'        => $fanclub->price,
            'purchase_type' => 'fanclub_join',
            'status'        => 'pending',
        ]);

        $idempotencyKey = (string) Str::uuid();

        try {
            $result = $this->paymentGateway->createCheckoutLink(
                amountYen:      $fanclub->price,
                itemName:       'ファンクラブ参加費：' . $fanclub->title,
                idempotencyKey: $idempotencyKey,
                redirectUrl:    $redirectUrl,
                metadata: [
                    'reference_id' => 'log:' . $paymentLog->id,
                    'buyer_email'  => $member->email ?? '',
                    'member_id'    => (string) $member->id,
                    'fanclub_id'   => (string) $fanclub->id,
                    'log_id'       => (string) $paymentLog->id,
                ]
            );
        } catch (\Throwable $e) {
            $paymentLog->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Fanclub checkout link creation failed', [
                'member_id'  => $member->id,
                'fanclub_id' => $fanclub->id,
                'log_id'     => $paymentLog->id,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }

        // order_id を payload に保存（Webhook で order_id → PaymentLog 照合に使用）
        $paymentLog->update([
            'payload' => array_merge((array) ($result['raw'] ?? []), [
                '_order_id' => $result['order_id'],
            ]),
        ]);

        // Dummy モード: status=completed → 即時参加確定
        if (strtolower($result['status']) === 'completed') {
            $this->applyFanclubJoin($paymentLog);
        }

        return [
            'checkout_url' => $result['checkout_url'],
            'payment_log'  => $paymentLog->fresh(),
        ];
    }

    /**
     * ファンクラブ参加を確定する（Webhook または即時処理から呼ばれる）
     *
     * 二重処理防止: processed_at が null の場合のみ処理する。
     * DB確定後に会員・キャスト・管理者へ通知メールを送信する。
     */
    public function applyFanclubJoin(PaymentLog $paymentLog): void
    {
        if ($paymentLog->isProcessed()) {
            return; // 既に処理済み
        }

        $applied = false;

        DB::transaction(function () use ($paymentLog, &$applied) {
            // 悲観ロックで二重処理を完全防止
            $log = PaymentLog::lockForUpdate()->find($paymentLog->id);
            if ($log === null || $log->isProcessed()) {
                return;
            }

            // ファンクラブ参加状態を登録（重複は upsert で防止）
            FanclubMembership::updateOrCreate(
                [
                    'fanclub_id' => $log->fanclub_id,
                    'member_id'  => $log->member_id,
                ],
                [
                    'status'         => 'active',
                    'payment_method' => 'card',
                    'started_at'     => now(),
                    'ended_at'       => null,
                    'last_paid_at'   => now(),
                ]
            );

            // payment_log を paid・処理済みに更新
            $log->update([
                'status'       => 'paid',
                'processed_at' => now(),
            ]);

            $applied = true;
        });

        if ($applied) {
            $this->sendJoinNotifications($paymentLog);
        }
    }

    /**
     * 入会確定後に会員・キャスト・管理者へ通知メールを送信する。
     */
    private function sendJoinNotifications(PaymentLog $paymentLog): void
    {
        try {
            $membership = FanclubMembership::with(['member', 'fanclub.cast'])
                ->where('fanclub_id', $paymentLog->fanclub_id)
                ->where('member_id', $paymentLog->member_id)
                ->first();

            if (!$membership) {
                return;
            }

            // 会員への通知
            $memberEmail = optional($membership->member)->routeNotificationForMail();
            if ($memberEmail) {
                Mail::to($memberEmail)->send(new FanclubPaymentSuccessMail($membership));
            }

            // キャストへの通知
            $cast = optional($membership->fanclub)->cast;
            if ($cast && !empty($cast->email)) {
                Mail::to($cast->email)->send(new FanclubJoinedCastMail($membership));
            }

            // 管理者への通知
            $adminEmail = config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new FanclubJoinedAdminMail($membership));
            }
        } catch (\Throwable $e) {
            Log::warning('Fanclub join notification mail failed', [
                'fanclub_id' => $paymentLog->fanclub_id,
                'member_id'  => $paymentLog->member_id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * 参加ステータスを変更する（退会・停止）
     *
     * 単発決済のため Square への解約 API 呼び出しは不要。
     */
    public function changeStatus(FanclubMembership $membership, string $status): FanclubMembership
    {
        $membership->status = $status;

        if ($status === 'canceled') {
            $membership->ended_at = now();
        }

        if ($status === 'active' && $membership->started_at === null) {
            $membership->started_at = now();
        }

        $membership->save();

        return $membership;
    }

    /**
     * 管理者による警告送信
     */
    public function sendWarning(FanclubMembership $membership, string $reason): FanclubMembership
    {
        $membership->warning_count    = ($membership->warning_count ?? 0) + 1;
        $membership->warning_sent_at  = now();
        $membership->suspension_reason = $reason;
        $membership->save();

        try {
            $member = $membership->member;
            if ($member && $member->email) {
                \Illuminate\Support\Facades\Mail::to($member->routeNotificationForMail())
                    ->send(new \App\Mail\FanclubWarningMail($membership, $reason));
            }
        } catch (\Throwable $e) {
            Log::warning('Warning mail failed', ['membership_id' => $membership->id, 'error' => $e->getMessage()]);
        }

        return $membership;
    }

    /**
     * 管理者による期間停止
     */
    public function suspend(FanclubMembership $membership, int $days, string $reason): FanclubMembership
    {
        $membership->status            = 'paused';
        $membership->suspended_until   = now()->addDays($days);
        $membership->suspension_reason = $reason;
        $membership->save();

        return $membership;
    }

    /**
     * 管理者による即時脱会
     */
    public function expel(FanclubMembership $membership, string $reason): FanclubMembership
    {
        $membership->suspension_reason = $reason;
        $membership->save();

        return $this->changeStatus($membership, 'canceled');
    }
}
