@extends('layouts.popup')

@section('content')

@if (session('pw_msg'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let msg = "{{ session('pw_msg') }}";

            // 성공 메시지면 창 닫기
            if (msg.includes('성공')) {
                alertc('확인 요청',msg,'p');
                setTimeout(() => {
                    window.close(); // 팝업 닫기
                    window.opener.location.reload();
                }, 2000); // 2초 뒤 홈으로 이동
            }
            else
            {
                alertc('확인 요청',msg);
            }
        });
    </script>
@endif

<div class="container mt-5">
    <div class="with mb-4">
        <h2 class="text-2xl font-bold">휴대폰 번호 변경</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
        <form id="phForm" action="{{ route('profile.update', $user->id) }}" method="POST">
            <input type="hidden" id="div" name="div" value="phC">
            @csrf
            <div class="mb-3">
                <label for="pw">휴대폰번호</label>
                <div class="form-text2 form-label" id="basic-addon4">✅ 하이픈('-') 제외하고 입력해주세요.</div>
                <div class="form-text2 form-label" id="basic-addon5">✅ 보안을 위해 현재 비밀번호를 입력해주세요.</div>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/ph.png" width="18">
                    </span>
                    <input type="text" class="form-control" id="ph" name="ph" oninput="onlyNumber(this)" maxlength=11>
                </div>
            </div>


            <div class="mb-3">
                <label for="pw2" class="form-label">비밀번호 확인</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/key.png" width="18">
                    </span>
                    <input type="password" class="form-control" id="pw" name="pw" oninput="onlyEngNum(this)" maxlength=20>
                </div>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-primary" style="float:right" onclick="checkAndSubmit2()">
                    변경
                </button>
            </div>

        </form>
        </div>
    </div>

</div>

<script>

function checkAndSubmit2()
{
    var ph = document.getElementById('ph').value;
    var pw = document.getElementById('pw').value;

    if ( ph == '' ) {
        alertc('확인 요청', '변경하실 휴대폰번호를 입력해주세요.');
        return false;
    }

    if ( ph.length < 11 ) {
        alertc('확인 요청', '휴대폰 번호를 확인해주세요.');
        return false;
    }

    if( pw == '' )
    {
        alertc('확인 요청', '비밀번호를 입력해주세요.');
        return false;
    }

    if ( pw.length < 8 ) 
    {
        alertc('확인 요청', '비밀번호는 최소 8자 이상이어야 합니다.');
        return false;
    }

    var rgex = /[!@#$%^&*(),.?":{}|<>]/;

    if ( !rgex.test(pw) ) {
        alertc('확인 요청', "비밀번호에 특수문자를 최소 1개 이상 포함해야 합니다.");
        return false;
    }

    document.getElementById('phForm').submit();

}
</script>
@endsection
