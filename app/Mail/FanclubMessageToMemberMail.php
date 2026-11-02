<?php

namespace App\Mail;

use App\Models\FanclubMessage;
use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FanclubMessageToMemberMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FanclubMessage $fanclubMessage) {}

    public function envelope(): Envelope
    {
        $shop = Shop::first();
        $shopName = $shop?->shop_name ?? 'ファンクラブ';
        return new Envelope(
            subject: "【{$shopName}】キャストからメッセージが届きました",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fanclub-message-to-member',
            with: ['fanclubMessage' => $this->fanclubMessage],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
