<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Models\Member;
use App\Models\Message;
use App\Models\GuestMessage;
use App\Models\Reservation;

class MailgunWebhookController extends Controller
{
    /**
     * Mailgun Webhookを受信してメッセージを処理
     */
    public function handleIncoming(Request $request)
    {
        try {
            // Mailgunの署名検証（セキュリティのため）
            if (!$this->verifyWebhookSignature($request)) {
                Log::warning('Mailgun webhook signature verification failed');
                return response('Unauthorized', 401);
            }

            // メールデータを取得
            $senderEmail = $request->input('sender');
            $subject = $request->input('subject', '');
            $bodyPlain = $request->input('body-plain', '');
            $bodyHtml = $request->input('body-html', '');
            $messageId = $request->input('Message-Id', '');

            // メッセージ本文を決定（プレーンテキスト優先）
            $messageBody = !empty($bodyPlain) ? $bodyPlain : strip_tags($bodyHtml);

            Log::info('Mailgun webhook received', [
                'sender' => $senderEmail,
                'subject' => $subject,
                'message_id' => $messageId
            ]);

            // 送信者が会員かどうか確認
            $member = $this->findMemberByEmail($senderEmail);

            if ($member) {
                // 会員の場合：messagesテーブルに保存
                $this->saveToMessages($member, $subject, $messageBody, $messageId);
                Log::info('Message saved to messages table for member', ['member_id' => $member->id]);
            } else {
                // 非会員の場合：guest_messagesテーブルに保存
                $this->saveToGuestMessages($senderEmail, $subject, $messageBody, $messageId);
                Log::info('Message saved to guest_messages table', ['email' => $senderEmail]);
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Mailgun webhook processing error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response('Internal Server Error', 500);
        }
    }

    /**
     * Mailgunの署名を検証
     */
    private function verifyWebhookSignature(Request $request)
    {
        $signingKey = config('services.mailgun.webhook_signing_key');

        if (!$signingKey) {
            Log::warning('Mailgun webhook signing key not configured');
            return true; // 設定されていない場合は検証をスキップ（開発環境用）
        }

        $timestamp = $request->input('timestamp');
        $token = $request->input('token');
        $signature = $request->input('signature');

        $expectedSignature = hash_hmac('sha256', $timestamp . $token, $signingKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * メールアドレスで会員を検索
     */
    private function findMemberByEmail($email)
    {
        // 暗号化されたメールアドレスで検索
        $members = Member::all();

        foreach ($members as $member) {
            try {
                $decryptedEmail = Crypt::decryptString($member->getRawOriginal('email'));
                if ($decryptedEmail === $email) {
                    return $member;
                }
            } catch (\Exception $e) {
                // 暗号化されていない場合の処理
                if ($member->getRawOriginal('email') === $email) {
                    return $member;
                }
            }
        }

        return null;
    }

    /**
     * 会員メッセージとして保存
     */
    private function saveToMessages($member, $subject, $messageBody, $messageId)
    {
        Message::create([
            'sender_id' => $member->id,
            'sender_type' => 'member',
            'receiver_id' => 1, // 管理者ID（適切な値に変更してください）
            'receiver_type' => 'admin',
            'message' => $messageBody,
            'title' => $subject,
        ]);
    }

    /**
     * 非会員メッセージとして保存
     */
    private function saveToGuestMessages($email, $subject, $messageBody, $messageId)
    {
        // 関連する予約を検索
        $reservation = $this->findReservationByEmail($email);

        GuestMessage::create([
            'email' => Crypt::encryptString($email),
            'sender_type' => 'guest',
            'message' => $messageBody,
            'title' => $subject,
            'reservation_id' => $reservation ? $reservation->id : null,
            'mailgun_message_id' => $messageId,
        ]);
    }

    /**
     * メールアドレスで予約を検索
     */
    private function findReservationByEmail($email)
    {
        $reservations = Reservation::where('member_id', 0)->get();

        foreach ($reservations as $reservation) {
            try {
                $decryptedEmail = Crypt::decryptString($reservation->getRawOriginal('email'));
                if ($decryptedEmail === $email) {
                    return $reservation;
                }
            } catch (\Exception $e) {
                // 暗号化されていない場合の処理
                if ($reservation->getRawOriginal('email') === $email) {
                    return $reservation;
                }
            }
        }

        return null;
    }
}
