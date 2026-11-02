<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\Member;
use Illuminate\Support\Facades\Crypt;

class MemberMessageToAdminNotification extends Mailable
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
     */
    public function envelope(): Envelope
    {
        // 会員のメールアドレスを取得
        $sender = Member::find($this->messageData->sender_id);
        $fromEmail = config('mail.from.address'); // デフォルト
        $fromName = '会員';

        if ($sender) {
            try {
                // 暗号化されたメールアドレスを復号化
                $decryptedEmail = Crypt::decryptString($sender->getRawOriginal('email'));
                $fromEmail = $decryptedEmail;
                $fromName = $sender->name ?? '会員';
            } catch (\Exception $e) {
                // 暗号化されていない場合はそのまま使用
                if ($sender->email) {
                    $fromEmail = $sender->email;
                    $fromName = $sender->name ?? '会員';
                }
            }
        }

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: '会員から新しいメッセージが届きました',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.member-message-to-admin',
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

    public function build()
    {
        // 会員のメールアドレスを取得
        $sender = Member::find($this->messageData->sender_id);
        $fromEmail = config('mail.from.address'); // デフォルト
        $fromName = '会員';

        if ($sender) {
            try {
                // 暗号化されたメールアドレスを復号化
                $decryptedEmail = Crypt::decryptString($sender->getRawOriginal('email'));
                $fromEmail = $decryptedEmail;
                $fromName = $sender->name ?? '会員';
            } catch (\Exception $e) {
                // 暗号化されていない場合はそのまま使用
                if ($sender->email) {
                    $fromEmail = $sender->email;
                    $fromName = $sender->name ?? '会員';
                }
            }
        }

        return $this->from($fromEmail, $fromName)
                    ->subject('会員から新しいメッセージが届きました')
                    ->view('emails.member-message-to-admin')
                    ->with([
                        'messageData' => $this->messageData,
                    ]);
    }
}


