<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecruitAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public string $fromEmail;
    public string $fromName;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData, string $fromEmail = '', string $fromName = '求人担当')
    {
        $this->mailData  = $mailData;
        $this->fromEmail = $fromEmail ?: config('mail.from.address');
        $this->fromName  = $fromName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【自動返信】求人応募ありがとうございます',
            from: new \Illuminate\Mail\Mailables\Address($this->fromEmail, $this->fromName),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recruit-auto-reply',
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
} 