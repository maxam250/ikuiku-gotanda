<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyMemberEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $url;
    /**
     * Create a new message instance.
     */
    public function __construct($url)
    {
        //
        $this->url = $url;
    }
    /**
     * 本登録のご案内
     */
    public function build()
    {
        return $this->subject('【本登録のご案内】')
                    ->view('emails.verify-member');
    }

    
}
