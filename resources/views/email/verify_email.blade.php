<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>[MJ] 이메일 인증</title>
</head>
<body style="font-family: 'Segoe UI', sans-serif; background-color: #f9f9f9; padding: 40px;">

    <div style="max-width: 600px; margin: 0 auto; background-color: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 30px;">
        <h2 style="color: #333;">안녕하세요, {{ $username }}님!</h2>
        <p style="font-size: 16px; color: #555;">
            MJ 서비스에 가입해주셔서 감사합니다.<br>
            아래 버튼을 클릭하셔서 이메일 인증을 완료해주세요.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" style="background-color: #4CAF50; color: white; padding: 14px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;">
                이메일 인증하기
            </a>
        </div>

        <p style="font-size: 14px; color: #888;">
            ※ 인증 링크는 60분 동안만 유효합니다.
        </p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <p style="font-size: 12px; color: #aaa;">
            이 메일은 발신전용입니다.<br>
            문의 사항이 있으시면 MJ 고객센터로 연락해주세요.
        </p>
    </div>

</body>
</html>