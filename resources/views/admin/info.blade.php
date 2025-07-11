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

<div class="container mt-4">
    <div class="with mb-3">
        <h2 class="text-2xl font-bold">@if($user->is_admin=='Y')관리자 @endif정보 변경</h2>
    </div>
    <div class="card mb-2">
        <div class="card-body">
            <form id="editForm" action="{{ route('admin.infoEdit', $user->id) }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label for="name">이름</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <img src="/img/user.png" width="18">
                        </span>
                        <input type="text" class="form-control" id="name" name="name" oninput="onlyStr(this)" value="{{ $user->user_name }}" >
                    </div>
                </div>

                <div class="mb-2">
                    <label for="email">이메일</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon2">
                            <img src="/img/email.png" width="18">
                        </span>
                        @php

                            if ( $user->email_verified_at === null) {
                                $d = 'readonly';
                            } else {
                                $d = '';
                            }
                        @endphp
                        <input type="email" class="form-control" id="email" name="email" oninput="onlyEmail(this)" value="{{ $user->email_r }}" {{ $d }} >
                    </div>
                </div>

                <div class="mb-2">
                    <label for="ph">휴대폰번호</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon3">
                            <img src="/img/ph.png" width="18">
                        </span>
                        <input type="text" class="form-control" id="ph" name="ph" oninput="onlyNumber(this)" value="{{ $user->ph_r }}" maxlength=11>
                    </div>
                </div>

                @if( $user->status == 'Y' )
                <div class="mb-3">
                    <label for="pw">비밀번호</label>
    
                    <div class="form-text2 form-label" id="basic-addon4">✅ 입력시에만 변경됩니다.</div>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon5">
                            <img src="/img/key.png" width="18">
                        </span>
                        <input type="password" class="form-control" id="pw" name="pw" oninput="onlyEngNum(this)" maxlength=20 >
                    </div>
                </div>

                    <div class="mb-2">
                        <button type="button" class="btn btn-primary" style="float:right" onclick="editChk()">
                            변경
                        </button>
                    </div>

                @else
                <div class="mb-3">
                    @php
                        if( $user->is_admin == 'Y' )
                            $nn = '관리자';
                        else
                            $nn = '회원';
                    @endphp
                    <div class="form-text2 form-label">탈퇴한 {{ $nn }}입니다.</div></label>
                </div>
                @endif
            </form>
            @if( $user->is_admin == 'Y' && auth()->user()->id != $user->id && $user->status == 'Y' )
            <div class="mb-2">
                <button class="btn btn-danger" onclick="confirmWithdraw('{{ route('admin.infoDel', $user->id) }}', 'GET')" >
                    관리자 탈퇴
                </button>
            </div>
            @endif 
        </div>
    </div>

    <!-- 탈퇴 확인 모달 -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="withdrawForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">관리자 탈퇴 확인</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    정말 탈퇴하시겠습니까? <br> 탈퇴 시 복구가 불가능합니다.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                    <button type="submit" class="btn btn-danger">탈퇴</button>
                </div>
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