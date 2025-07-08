@extends('layouts.popup')

@section('content')

@if (session('pw_msg'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let msg = "{{ session('pw_msg') }}";

            // 성공 메시지면 창 닫기
            if (msg.includes('완료')) {
                alertc('완료',msg,'p');
                setTimeout(() => {
                    window.close();
                    window.opener.location.reload();
                }, 2000);
            }
            else
            {
                alertc('확인 요청',msg);
            }
        });
    </script>
@endif

<div class="container mt-5">
    <div class="with mb-4" id="popup-name">
        <h2 class="text-2xl font-bold">관리자 계정 생성</h2>
    </div>

    <div class="card mb-4" id="form-section">
        <div class="card-body">
        <form method="POST" action="{{ route('admin.add') }}" onsubmit="return handleSubmit(this);">
            @csrf
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/user.png" width="18">
                    </span>
                    <input type="text" name="name" class="form-control" placeholder="이름" id="name" required oninput="onlyStr(this)" maxlength=20>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/email.png" width="18">
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="이메일" id="email" required oninput="onlyEmail(this);">
                </div>
                @error('email')
                    <div class="form-text2" id="basic-addon5">✅ 이메일을 확인해주세요.</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/ph.png" width="18">
                    </span>
                    <input type="text" name="ph" class="form-control" id="ph" placeholder="휴대폰번호" required oninput="onlyNumber(this)" maxlength="11">
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/key.png" width="18">
                    </span>
                    <input type="password" name="pw" class="form-control" placeholder="비밀번호" id="pw" required>
                </div>
                @error('pw')
                    <div class="form-text2 form-label" id="basic-addon4">✅ 비밀번호는 8자 이상(특수문자 1개 포함) 으로 작성해주세요.</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <button type="submit" class="btn btn-primary" style="float:right">
                    생성
                </button>
            </div>
        </form>
        </div>
    </div>
    
</div>
<script>
function infoChk()
{
    var name = document.getElementById('name').value;
    var email = document.getElementById('email').value;
    var ph = document.getElementById('ph').value;
    var pw = document.getElementById('pw').value;

    if( name === '' )
    {
        alertc('확인 요청','이름을 입력해주세요.');
        return false;
    }
    else if( email === '')
    {
        alertc('확인 요청','이메일을 입력해주세요.');
        return false;
    }
    else if( ph === '' )
    {
        alertc('확인 요청','휴대폰 번호를 입력해주세요.');
        return false;
    }
    else if( ph.length < 10)
    {
        alertc('확인 요청','휴대폰 번호를 확인해주세요.');
        return false;
    }
    else if ( pw.length < 8 ) {
        alertc('확인 요청','비밀번호는 최소 8자 이상이어야 합니다.');
        return false;
    }
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        alertc('확인 요청','이메일 형식이 올바르지 않습니다.');
        return false;
    }

    return true;
}

</script>

@endsection
