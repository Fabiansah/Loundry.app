<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Kode OTP Verifikasi Akun Kasir')
                    ->html("<h2>Kode OTP Anda: <b>{$this->otp}</b></h2><p>Kode ini berlaku selama 10 menit.</p>");
    }
}