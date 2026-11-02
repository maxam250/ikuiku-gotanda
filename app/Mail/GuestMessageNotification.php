<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\GuestMessage;

class GuestMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $guestMessage;
    public $fromEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(GuestMessage $guestMessage, $fromEmail)
    {
        $this->guestMessage = $guestMessage;
        $this->fromEmail = $fromEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromEmail,
            subject: $this->guestMessage->title ?: '新しいメッセージが届きました',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.guest_message_notification',
            with: [
                'message' => $this->guestMessage,
                'messageBody' => $this->guestMessage->message,
                'title' => $this->guestMessage->title,
            ]
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
