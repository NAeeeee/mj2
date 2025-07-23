@extends('layouts.app2')

@section('content')
<section class="container py-5" style="max-width: 480px;">
    <h2 class="mb-4">비밀번호 재설정</h2>

    {{-- 세션 상태 메시지 --}}
    @if( session('type') == 'success' )
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                alertc('완료',"비밀번호 변경을 완료했습니다.",'p');
                setTimeout(function() {
                    location.href = '/';
                }, 3000);
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

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email', $request->email) }}" 
                   required autofocus autocomplete="username" readonly>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Confirmation -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" 
                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                   name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                비밀번호 재설정 하기
            </button>
        </div>
    </form>
</section>
@endsection
