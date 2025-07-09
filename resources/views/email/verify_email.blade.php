<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>이메일 인증</title>
</head>
<body>
    <h1>안녕하세요, {{ $username }}님!</h1>
    <p>아래 버튼을 눌러 이메일 인증을 완료해주세요.</p>

    <p>
        <a href="{{ $verificationUrl }}" style="padding: 10px 20px; background-color: #3478f6; color: white; text-decoration: none; border-radius: 5px;">
            이메일 인증하기
        </a>
    </p>

    <p>감사합니다.<br>MJNA Dev 팀</p>
</body>
</html>
