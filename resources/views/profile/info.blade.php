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
        <h2 class="text-2xl font-bold">정보 변경</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
        <form id="pwForm" action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3 mt-1" style="text-align: center;">
                <img id="profile-img" class="rounded-circle" style="height: 8rem;witdh: 8rem;cursor:pointer;@if(empty($img)) border: solid; @endif""
                                        src="{{ isset($img['pathDate'], $img['savename']) && ($img['target_type'] ?? '') === 'I'
                                            ? asset('storage/img/' . $img['pathDate'] . '/' . $img['savename'])
                                            : asset('img/pro.png') }}">
                <input type="file" id="file-input" name="file" accept="image/*" style="display: none;" />
            </div>

            <div class="mb-3">
                <label for="pw">휴대폰번호</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/ph.png" width="18">
                    </span>
                    <input type="text" class="form-control" id="ph" name="ph" oninput="onlyNumber(this)" maxlength=11 value="{{ $user->ph ?? '' }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="pw">비밀번호</label>
                <div class="form-text2 form-label" id="basic-addon1">✅ 비밀번호는 입력시에만 변경됩니다.</div>
                <div class="form-text2 form-label" id="basic-addon2">✅ 최소8자이상(특수문자 포함) 현재와 다른 비밀번호를 사용하셔야합니다.</div>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/key.png" width="18">
                    </span>
                    <input type="password" class="form-control" id="pw" name="pw" maxlength=20>
                </div>
            </div>

            <div class="mb-3">
                <label for="pw2" class="form-label">비밀번호 확인</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/key.png" width="18">
                    </span>
                    <input type="password" class="form-control" id="pw2" name="pw_confirmation" maxlength=20>
                </div>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-primary" style="float:right" onclick="checkAndSubmit()">
                    변경
                </button>
            </div>

        </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>

    document.getElementById("profile-img").addEventListener("click", function () {
    document.getElementById("file-input").click(); // 클릭 시 input 열기
});

document.getElementById("file-input").addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
        document.getElementById("profile-img").src = event.target.result; // 미리보기
    };
    reader.readAsDataURL(file);

    // 실제 서버로 업로드는 AJAX 또는 폼으로 따로 처리
  }
});

function checkAndSubmit()
{
    let ph = document.getElementById('ph').value.trim();
    let pw = document.getElementById('pw').value.trim();
    let pw2 = document.getElementById('pw2').value.trim();
    let rgex = /[!@#$%^&*(),.?":{}|<>]/;

    if( ph != '' )
    {
        ph = ph.replace(/-/g, '');
        if( ph.length != 11 )
        {
            alertc('확인 요청', '전화번호를 확인해주세요.');
            return false;
        }
    }

    if( pw != '' )
    {
        if( pw2 == '' )
        {   
            alertc('확인 요청', '변경할 비밀번호를 입력해주세요.');
            return false;
        }
        if ( pw.length < 8 ) {
            alertc('확인 요청', '비밀번호는 최소 8자 이상이어야 합니다.');
            return false;
        }
        if ( pw !== pw2 ) {
            alertc('확인 요청', '비밀번호가 일치하지 않습니다.');
            return false;
        }

        if ( !rgex.test(pw) ) {
            alertc('확인 요청', "비밀번호에 특수문자를 최소 1개 이상 포함해야 합니다.");
            return false;
        }   
    }

    document.getElementById('pwForm').submit();

}
</script>
@endpush
