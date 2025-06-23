@extends('layouts.popup')

@section('content')
<div class="container mt-5">
    <div class="with mb-4">
        <h2 class="text-2xl font-bold">이메일 변경</h2>
    </div>
    <div class="card mb-4">
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
</div>
<script>
function handleSubmit(form) {
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(response => {
        if (response.ok) {
            alertc('','이메일 변경 완료. 인증 메일이 전송되었습니다!','p');
            if (window.opener)
            {
                setTimeout(function() {
                    window.opener.location.reload();
                    window.close();
                }, 3000);
            }
        } else {
            alertc('오류','오류가 발생했습니다. 관리자에게 문의해주세요.');
        }
    });

    return false; // 폼 기본 제출 막기
}
</script>

@endsection
