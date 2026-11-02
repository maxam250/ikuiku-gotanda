<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public NewsletterSubscriber $subscriber;
    protected string $mailSubject;
    protected ?string $bodyText;
    protected ?string $bodyHtml;

    /**
     * Create a new message instance.
     */
    public function __construct(NewsletterSubscriber $subscriber, string $subject, ?string $bodyText = null, ?string $bodyHtml = null)
    {
        $this->subscriber = $subscriber;
        $this->mailSubject = $subject;
        $this->bodyText = $bodyText;
        $this->bodyHtml = $bodyHtml;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = 'newsletter.emails.campaign-text';
        $viewData = [
            'subscriber'     => $this->subscriber,
            'subject'        => $this->mailSubject,
            'body'           => $this->bodyText,
            'unsubscribeUrl' => route('newsletter.unsubscribe', $this->subscriber->token),
        ];

        // HTML本文がある場合はHTMLビューを使用
        if ($this->bodyHtml) {
            $view = 'newsletter.emails.campaign-html';
            $viewData['body'] = $this->bodyHtml;
        }

        return new Content(
            view: $view,
            with: $viewData,
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
