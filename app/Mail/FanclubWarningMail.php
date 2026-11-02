<?php

namespace App\Mail;

use App\Models\FanclubMembership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FanclubWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly FanclubMembership $membership,
        public readonly string $reason
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【' . config('app.name') . '】ファンクラブ利用規約違反に関するご連絡',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fanclub.warning',
        );
    }
}
