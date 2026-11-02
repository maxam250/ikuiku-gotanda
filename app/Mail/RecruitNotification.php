<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecruitNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject('求人応募のお知らせ')
                    ->view('emails.recruit-notification');

        // 添付ファイルを追加
        if (!empty($this->mailData['attachments'])) {
            foreach ($this->mailData['attachments'] as $attachment) {
                if (file_exists($attachment)) {
                    $mail->attach($attachment);
                }
            }
        }

        return $mail;
    }
} 