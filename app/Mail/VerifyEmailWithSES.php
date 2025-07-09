<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    public function __construct($user)
    {
        $this->user = $user;
        $this->verificationUrl = $this->generateVerificationUrl($user);
    }

    protected function generateVerificationUrl($user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );
    }

    public function build()
    {
        return $this->subject('[MJ] 이메일 인증을 완료해주세요')
                    ->view('email.verify_email')  // ← 여기에 실제 뷰 경로
                    ->with([
                        'username' => $this->user->name,
                        'verificationUrl' => $this->verificationUrl,
                    ])
                    ->withSwiftMessage(function ($message) {
                        $message->getHeaders()
                                ->addTextHeader('X-SES-CONFIGURATION-SET', 'my-first-configuration-set');
                    });
    }
}
