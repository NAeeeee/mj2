# 📧 이메일 인증 처리 흐름 (Laravel + AWS SES)

## 1. 회원가입 → 인증 메일 전송(운영 서버)

회원가입 시 `RegisteredUserController.php`에서 아래와 같이 인증 메일을 발송

```php
Mail::to($user->email)->send(new VerifyEmailWithSES($user));
```

---

## 2. 인증 메일 클래스 (`App\Mail\VerifyEmailWithSES.php`)

```php
class VerifyEmailWithSES extends Mailable
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
                    ->view('email.verify_email')
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
```

---

## 3. 인증 링크 클릭 시 흐름

* 메일 내 인증 링크: `/email/verify/{id}/{hash}`
* `routes/auth.php`에 등록된 `verification.verify` 라우트에서 처리
* `VerifyEmailController`가 링크를 검증하고, `users.email_verified_at` 컬럼을 갱신하여 인증 완료 처리

---

## 4. 미들웨어 및 제한 처리

* 인증되지 않은 사용자는 `verified` 미들웨어에 의해 기능 접근 제한
* 인증된 사용자만 주요 기능 접근 가능 (ex: 게시물 작성, 쪽지 전송 등)
* 인증 링크는 60분 유효 / URL 서명 기반으로 위조 방지
* Laravel 기본 제공 인증 시스템을 기반으로 확장 구현함

---

## 5. SES 구성 요약

* AWS SES에서 도메인 및 발신 이메일 인증 완료 후 사용
* 인증 메일/비밀번호 재설정 메일 모두 SES를 통해 발송

---

## ✅ 참고

* 인증 메일 뷰는 `resources/views/email/verify_email.blade.php` 에 위치
* 비밀번호 재설정 뷰는 `resources/views/email/reset-password.blade.php` 에 위치
* SES 설정은 `.env`에 다음과 같이 명시:

```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.ap-northeast-2.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=발급받은 user
MAIL_PASSWORD=발급받은 pass
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admin@mjnadev.com
MAIL_FROM_NAME="MJ 프로젝트"
```

