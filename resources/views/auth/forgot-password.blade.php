@extends('layouts.app2')

@section('content')
<section class="container py-5" style="max-width: 480px;">
    <h2 class="mb-5">비밀번호 재설정</h2>

    <div class="mb-4 text-secondary">
        비밀번호를 잊으셨나요? 걱정하지 마세요. 이메일 주소를 입력해주시면 새 비밀번호를 선택하실 수 있는 비밀번호 재설정 링크를 이메일로 보내드리겠습니다.
    </div>

    {{-- 세션 상태 메시지 --}}
    @if( session('type') == 'success' )
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                alertc('완료',"{{ __('passwords.sent') }}",'p');
            });
        </script>
    @endif

    @if( session('type') == 'error' )
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                alertc('확인 요청',"{{ session('message') }}");
            });
        </script>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" 
                   class="form-control @error('email') is-invalid @enderror
                   @if(session('type') == 'success') is-valid @endif" 
                   name="email" value="{{ old('email') }}" required autofocus>
            @if( session('type') == 'success' )
                <div class="valid-feedback">{{ __('passwords.sent') }}</div>
            @endif
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                비밀번호 재설정 링크 보내기
            </button>
        </div>
    </form>
</section>
@endsection
