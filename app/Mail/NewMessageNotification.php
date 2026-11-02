<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\Shop;
use Illuminate\Mail\Mailables\Address;

class NewMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $messageData;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message)
    {
        $this->messageData = $message;
    }

    /**
     * Get the message envelope.
     *
     * Reply-To: reply@{shop_domain}（固定）
     * スレッド識別は Message-ID / In-Reply-To ヘッダーで行う。
     */
    public function envelope(): Envelope
    {
        $shop     = Shop::first();
        $shopName = $shop ? $shop->shop_name : 'ショップ';

        $subject = '新しいメッセージが届きました';
        if ($this->messageData->sender_type === 'admin') {
            $subject = "【{$shopName}】新しいメッセージが届きました";
        }

        // Reply-To: reply@{domain} + shop_mail
        $replyTo = [];
        if ($shop && $shop->shop_domain) {
            $replyTo[] = new Address("reply@{$shop->shop_domain}");
            if ($shop->shop_mail) {
                $replyTo[] = new Address($shop->shop_mail);
            }
        }

        return new Envelope(
            subject: $subject,
            replyTo: $replyTo,
        );
    }

    /**
     * カスタムヘッダーを追加。
     *
     * Message-ID: <UUID@domain> 形式に設定。
     *   返信メールの In-Reply-To ヘッダーからUUIDを抽出するために使用。
     *   Gmail は X-Thread-ID を削除するが In-Reply-To は必ず保持する。
     * X-Thread-ID: UUID を埋め込む（補助）。
     */
    public function headers(): Headers
    {
        $custom = [];
        if ($this->messageData->thread_uuid ?? null) {
            $custom['X-Thread-ID'] = $this->messageData->thread_uuid;
        }

        return new Headers(text: $custom);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-message',
            with: [
                'messageData' => $this->messageData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * build() フォールバック（envelope()/headers() が使われない環境用）
     */
    public function build()
    {
        $shop     = Shop::first();
        $shopName = $shop ? $shop->shop_name : 'ショップ';

        $subject = '新しいメッセージが届きました';
        if ($this->messageData->sender_type === 'admin') {
            $subject = "【{$shopName}】新しいメッセージが届きました";
        }

        $mail = $this->subject($subject)
                     ->view('emails.new-message')
                     ->with(['messageData' => $this->messageData]);

        // Reply-To: reply@{domain} + shop_mail（envelope と同様）
        if ($shop && $shop->shop_domain) {
            $mail->replyTo("reply@{$shop->shop_domain}");
            if ($shop->shop_mail) {
                $mail->replyTo($shop->shop_mail);
            }
        }

        // X-Thread-ID と Message-ID を Symfony 経由で設定
        if ($this->messageData->thread_uuid ?? null) {
            $uuid   = $this->messageData->thread_uuid;
            $domain = ($shop && $shop->shop_domain) ? $shop->shop_domain : 'example.com';
            $mail->withSymfonyMessage(function ($message) use ($uuid, $domain) {
                $headers = $message->getHeaders();
                $headers->remove('Message-ID');
                $headers->addIdHeader('Message-ID', "{$uuid}@{$domain}");
            });
        }

        return $mail;
    }
}
