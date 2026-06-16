<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $email;

    public function __construct(string $otp, string $email)
    {
        $this->otp   = $otp;
        $this->email = $email;
    }

    public function build()
    {
        return $this->subject('UCC-IMS — Email Verification Code')
                    ->view('emails.otp');
    }
}