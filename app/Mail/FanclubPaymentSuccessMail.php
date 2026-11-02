<?php

namespace App\Mail;

use App\Models\FanclubMembership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FanclubPaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly FanclubMembership $membership
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【' . config('app.name') . '】ファンクラブ会費のお支払いが完了しました',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fanclub.payment_success',
        );
    }
}
