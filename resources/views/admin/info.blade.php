@extends('layouts.popup')

@section('content')

@if (session('pw_msg'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let msg = "{{ session('pw_msg') }}";

            // 성공 메시지면 창 닫기
            if (msg.includes('수정')) {
                alertc('완료',msg,'p');
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
        <h2 class="text-2xl font-bold">@if($user->is_admin=='Y')관리자 @endif정보 변경</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
        <form id="editForm" action="{{ route('admin.infoEdit', $user->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name">이름</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/user.png" width="18">
                    </span>
                    <input type="text" class="form-control" id="name" name="name" oninput="onlyEngNum(this)" value="{{ $user->name }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="email">이메일</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon2">
                        <img src="/img/email.png" width="18">
                    </span>
                    @php

                        if ( $user->email_verified_at === null) {
                            $d = 'disabled';
                        } else {
                            $d = '';
                        }
                    @endphp
                    <input type="email" class="form-control" id="email" name="email" oninput="onlyEmail(this)" value="{{ $user->email }}" {{ $d }}>
                </div>
            </div>

            <div class="mb-3">
                <label for="ph">휴대폰번호</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon3">
                        <img src="/img/ph.png" width="18">
                    </span>
                    <input type="text" class="form-control" id="ph" name="ph" oninput="onlyNumber(this)" value="{{ $user->ph }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="pw">비밀번호</label>
  
            <div class="form-text2 form-label" id="basic-addon4">✔ 입력시에만 변경됩니다.</div>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon5">
                        <img src="/img/key.png" width="18">
                    </span>
                    <input type="password" class="form-control" id="pw" name="pw" oninput="onlyEngNum(this)" maxlength=20 >
                </div>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-primary" style="float:right" onclick="editChk()">
                    변경
                </button>
            </div>

        </form>
        </div>
    </div>

</div>

<script>
function editChk()
{
    $("#editForm").submit();
}
</script>
@endsection