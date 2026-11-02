<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSquareWebhookJob;
use App\Models\SquareWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Square Webhook 受信コントローラ
 *
 * 受信URL: POST /payment/webhook/square
 *
 * 対象イベント:
 *   - payment.created
 *   - payment.updated
 *
 * 処理フロー:
 *   1. 署名検証（HMAC-SHA256）
 *   2. event_id で重複受信チェック
 *   3. SquareWebhookLog に記録
 *   4. ProcessSquareWebhookJob をキューに投入
 */
class SquareWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        if (!$this->verifySignature($request)) {
            Log::warning('Square webhook signature verification failed', ['ip' => $request->ip()]);
            return response('Unauthorized', 401);
        }

        $payload   = $request->json()->all();
        $eventId   = data_get($payload, 'event_id');
        $eventType = data_get($payload, 'type', '');

        // 重複受信チェック（冪等性）
        if ($eventId && SquareWebhookLog::where('event_id', $eventId)->exists()) {
            return response('OK', 200);
        }

        $log = SquareWebhookLog::create([
            'event_id'   => $eventId,
            'event_type' => $eventType,
            'payload'    => $payload,
            'status'     => 'received',
        ]);

        ProcessSquareWebhookJob::dispatch($log->id);

        return response('OK', 200);
    }

    private function verifySignature(Request $request): bool
    {
        $signatureKey = config('fanclub.payments.square.webhook_signature_key');

        if (empty($signatureKey)) {
            return config('app.env') !== 'production';
        }

        $signature       = $request->header('x-square-hmacsha256-signature', '');
        $notificationUrl = config('app.url') . '/payment/webhook/square';
        $body            = $request->getContent();

        $expected = base64_encode(hash_hmac('sha256', $notificationUrl . $body, $signatureKey, true));

        return hash_equals($expected, $signature);
    }
}