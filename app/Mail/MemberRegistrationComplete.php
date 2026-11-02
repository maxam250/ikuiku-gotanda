<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Member;

class MemberRegistrationComplete extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $shops;

    /**
     * Create a new message instance.
     */
    public function __construct(Member $member, $shops = null)
    {
        $this->member = $member;
        $this->shops = $shops ?: \App\Models\Shop::first();
    }

    /**
     * 会員登録完了のお知らせ
     */
    public function build()
    {
        return $this->subject('【会員登録完了のお知らせ】')
                    ->view('emails.member-registration-complete');
    }
}
