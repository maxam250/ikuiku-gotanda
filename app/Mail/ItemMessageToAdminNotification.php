<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\MessageItem;
use App\Models\Item;
use Illuminate\Support\Facades\Crypt;

class ItemMessageToAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $messageData;

    /**
     * Create a new message instance.
     */
    public function __construct(MessageItem $message)
    {
        $this->messageData = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // キャストのメールアドレスを取得
        $sender = Item::find($this->messageData->sender_id);
        $fromEmail = config('mail.from.address'); // デフォルト
        $fromName = 'キャスト';

        if ($sender) {
            try {
                // 暗号化されたメールアドレスを復号化
                $decryptedEmail = Crypt::decryptString($sender->getRawOriginal('email'));
                $fromEmail = $decryptedEmail;
                $fromName = $sender->name ?? 'キャスト';
            } catch (\Exception $e) {
                // 暗号化されていない場合はそのまま使用
                if ($sender->email) {
                    $fromEmail = $sender->email;
                    $fromName = $sender->name ?? 'キャスト';
                }
            }
        }

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: 'キャストから新しいメッセージが届きました',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.item-message-to-admin',
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
        // キャストのメールアドレスを取得
        $sender = Item::find($this->messageData->sender_id);
        $fromEmail = config('mail.from.address'); // デフォルト
        $fromName = 'キャスト';

        if ($sender) {
            try {
                // 暗号化されたメールアドレスを復号化
                $decryptedEmail = Crypt::decryptString($sender->getRawOriginal('email'));
                $fromEmail = $decryptedEmail;
                $fromName = $sender->name ?? 'キャスト';
            } catch (\Exception $e) {
                // 暗号化されていない場合はそのまま使用
                if ($sender->email) {
                    $fromEmail = $sender->email;
                    $fromName = $sender->name ?? 'キャスト';
                }
            }
        }

        return $this->from($fromEmail, $fromName)
                    ->subject('キャストから新しいメッセージが届きました')
                    ->view('emails.item-message-to-admin')
                    ->with([
                        'messageData' => $this->messageData,
                    ]);
    }
}


