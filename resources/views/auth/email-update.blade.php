@extends('layouts.popup')

@section('content')
<div class="container mt-5">
    <div class="with mb-4" id="popup-name">
        <h2 class="text-2xl font-bold">이메일 변경</h2>
    </div>

    <div class="card mb-4" id="form-section">
        <div class="card-body">
        <form method="POST" action="{{ route('email.update') }}" onsubmit="return handleSubmit(this);">
            @csrf
            <div class="mb-3">
                <label for="pw2" class="form-label">이메일</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="/img/email.png" width="18">
                    </span>
                    <input type="email" name="email" class="form-control" id="email" required>
                </div>
            </div>
            <!-- <input type="email" name="email" required placeholder="새 이메일 입력"> -->
            <div class="mb-4">
                <button type="submit" class="btn btn-primary" style="float:right">
                    변경
                </button>
            </div>
        </form>
        </div>
    </div>
    
    <div id="loading-section" style="display:none;">
        <div class="d-flex justify-content-center align-items-center" style="height: 350px;">
            <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
<script>
function handleSubmit(form) {
    var popUpName = document.getElementById('popup-name');
    var formSection = document.getElementById('form-section');
    var loadingSection = document.getElementById('loading-section');

    popUpName.style.display = 'none';
    formSection.style.display = 'none';
    loadingSection.style.display = 'block';

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(response => {
        if (response.ok) {
            alertc('성공','이메일 변경 완료. 인증 메일이 전송되었습니다!','p');
            if (window.opener)
            {
                setTimeout(function() {
                    window.opener.location.reload();
                    window.close();
                }, 2000);
            }
        } 
        else if (response.status === 422) {
            // 유효성 검증 실패
            alertc('확인 요청','이미 사용중인 이메일입니다. 다른 메일을 사용해주세요');
            popUpName.style.display = 'block';
            formSection.style.display = 'block';
            loadingSection.style.display = 'none';
        }
        else {
            alertc('오류','오류가 발생했습니다. 관리자에게 문의해주세요.');
            popUpName.style.display = 'block';
            formSection.style.display = 'block';
            loadingSection.style.display = 'none';
        }
    });

    return false; // 폼 기본 제출 막기
}
</script>

@endsection
