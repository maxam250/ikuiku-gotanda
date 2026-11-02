<?php

namespace App\Jobs;

use App\Models\PaymentLog;
use App\Models\SquareWebhookLog;
use App\Services\Fanclub\FanclubMembershipService;
use App\Services\Payment\PointPurchaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSquareWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries  = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly int $webhookLogId
    ) {}

    public function handle(
        FanclubMembershipService $membershipService,
        PointPurchaseService     $pointService
    ): void {
        $log = SquareWebhookLog::find($this->webhookLogId);
        if (!$log) {
            return;
        }

        try {
            $this->process($log, $membershipService, $pointService);
            $log->status = 'processed';
            $log->save();
        } catch (\Throwable $e) {
            $log->status        = 'failed';
            $log->error_message = $e->getMessage();
            $log->save();

            Log::error('Square webhook processing failed', [
                'webhook_log_id' => $this->webhookLogId,
                'event_type'     => $log->event_type,
                'error'          => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function process(
        SquareWebhookLog         $log,
        FanclubMembershipService $membershipService,
        PointPurchaseService     $pointService
    ): void {
        match ($log->event_type) {
            'payment.created',
            'payment.updated' => $this->handlePaymentEvent($log->payload, $membershipService, $pointService),
            default           => $this->skip($log),
        };
    }

    private function handlePaymentEvent(
        array                    $payload,
        FanclubMembershipService $membershipService,
        PointPurchaseService     $pointService
    ): void {
        $payment   = data_get($payload, 'data.object.payment', []);
        $status    = strtolower((string) data_get($payment, 'status', ''));
        $paymentId = (string) data_get($payment, 'id', '');

        if ($status !== 'completed') {
            Log::info('Square webhook skipped (not completed)', ['payment_id' => $paymentId, 'status' => $status]);
            return;
        }

        if (empty($paymentId)) {
            Log::warning('Square webhook: payment ID missing');
            return;
        }

        $paymentLog = PaymentLog::where('square_payment_id', $paymentId)->first();

        // ① payment_id で見つからない場合: メタデータの log_id で探す（旧 Payments API フロー）
        if (!$paymentLog) {
            $logId = data_get($payment, 'metadata.log_id')
                  ?? $this->extractLogId(data_get($payment, 'reference_id', ''));

            if ($logId) {
                $paymentLog = PaymentLog::find((int) $logId);
                if ($paymentLog && empty($paymentLog->square_payment_id)) {
                    $paymentLog->update(['square_payment_id' => $paymentId]);
                }
            }
        }

        // ② それでも見つからない場合: payload._order_id で探す（Payment Links フロー）
        if (!$paymentLog) {
            $orderId = data_get($payment, 'order_id');
            if ($orderId) {
                $paymentLog = PaymentLog::where('payload->_order_id', $orderId)->first();
                if ($paymentLog && empty($paymentLog->square_payment_id)) {
                    $paymentLog->update(['square_payment_id' => $paymentId]);
                }
            }
        }

        if (!$paymentLog) {
            Log::warning('Square webhook: PaymentLog not found', [
                'payment_id' => $paymentId,
                'order_id'   => data_get($payment, 'order_id'),
                'metadata'   => data_get($payment, 'metadata'),
            ]);
            return;
        }

        if ($paymentLog->isProcessed()) {
            Log::info('Square webhook: already processed', ['log_id' => $paymentLog->id]);
            return;
        }

        $paymentLog->update(['payload' => $payload]);

        match ($paymentLog->purchase_type) {
            'fanclub_join'   => $this->processFanclubJoin($paymentLog, $membershipService),
            'point_purchase' => $this->processPointPurchase($paymentLog, $pointService),
            default          => Log::warning('Unknown purchase_type', [
                'log_id'        => $paymentLog->id,
                'purchase_type' => $paymentLog->purchase_type,
            ]),
        };
    }

    private function processFanclubJoin(
        PaymentLog               $paymentLog,
        FanclubMembershipService $membershipService
    ): void {
        $membershipService->applyFanclubJoin($paymentLog);

        Log::info('Fanclub join applied via webhook', [
            'log_id'     => $paymentLog->id,
            'member_id'  => $paymentLog->member_id,
            'fanclub_id' => $paymentLog->fanclub_id,
        ]);
    }

    private function processPointPurchase(
        PaymentLog           $paymentLog,
        PointPurchaseService $pointService
    ): void {
        $pointService->applyPoints($paymentLog);

        Log::info('Points applied via webhook', [
            'log_id' => $paymentLog->id,
            'member_id' => $paymentLog->member_id,
            'points' => $paymentLog->points,
        ]);
    }

    private function extractLogId(string $referenceId): ?string
    {
        if (preg_match('/^log:(\d+)$/', $referenceId, $m)) {
            return $m[1];
        }
        return null;
    }

    private function skip(SquareWebhookLog $log): void
    {
        $log->status = 'skipped';
        $log->save();
    }
}
