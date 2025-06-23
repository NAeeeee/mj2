✅ 이메일 전체 흐름 요약
1. 회원가입 하면 User 모델이 MustVerifyEmail을 구현하고 있어서
Laravel이 자동으로 이메일 인증 메일을 사용자에게 발송함

2. 사용자가 받은 이메일의 링크를 클릭하면
라우트 /email/verify/{id}/{hash} 로 이동
EmailVerificationRequest가 해당 사용자의 서명을 검증하고
->fulfill() 메서드가 사용자 모델의 email_verified_at 필드를 채움 (즉, 인증 완료)

3. 그 이후 verified 미들웨어를 걸어둔 페이지에 들어갈 수 있게 됨
인증 안 한 사용자는 /email/verify 페이지로 계속 리디렉트됨



✅ 각 구성 요소 설명
1. MustVerifyEmail
이 인터페이스를 User 모델에 붙이면 Laravel이 "이 사용자는 이메일 인증이 필요해"라고 판단
이게 있어야 인증 메일 자동 발송 + 인증 체크 기능들이 작동함.

2. 인증 메일 발송
회원가입 후 sendEmailVerificationNotification() 메서드가 자동 실행돼서 인증 링크가 담긴 메일을 발송함.
링크에는 사용자 ID, 서명, 해시 정보가 담겨 있어서 보안적으로 안전함.

3. 인증 라우트
/email/verify/{id}/{hash} 이 주소는 메일 속 인증 링크에서 넘어옴.
여기서 EmailVerificationRequest가 요청 유효성(서명, 해시)을 확인한 후
$request->fulfill() 을 호출해서 인증 처리 (DB에 email_verified_at 기록)

4. verified 미들웨어
이 미들웨어가 붙은 라우트는 이메일 인증을 완료한 사용자만 접근 가능.
인증 안 했으면 /email/verify 페이지로 리디렉션됨.
