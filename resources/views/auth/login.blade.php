@extends('layouts.app')

@section('content')
    <!-- Session Status -->
    <section class="pt-4 mt-3">
        @if ($errors->any())
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    alertc("로그인 실패", "{{ $errors->first() }}");
                });
            </script>
        @endif
        <div class="container px-lg-5">

        <form id="loginForm" method="POST" action="{{ route('login') }}" class="mt-4" onsubmit="return formChk()">
            @csrf
            <div class="row mb-5">
                <label for="email" class="form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="email" name="email" oninput="onlyEmail(this);">
                </div>
            </div>

            <!-- Password -->
            <div class="row mb-5">
                <label for="password" class="form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" id="password" name="password" class="form-control" aria-describedby="passwordHelpInline">
                </div>
                @error('password')
                <div class="mb-4 font-medium text-sm text-red-600">
                    {{ $message }}
                </div>
                   
                @enderror
            </div>


            <div class="flex items-center justify-end mt-4 mb-7">
                <button type="button" class="btn btn-secondary" style="margin-right:15px;" onclick="reg();">
                    {{ __('register') }}
                </button>

                <button type="submit" class="btn btn-primary">
                    {{ __('login') }}
                </button>
            </div>
        </form>
        </div>
    </section>

<script>
    function reg()
    {
        location.href="/register";
    }

    function formChk() 
    {
        var email = document.getElementById("email").value.trim();
        var pw = document.getElementById("password").value.trim();
        var rgex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;;

        if ( !email || !pw ) {
            alertc("확인 요청", "이메일과 비밀번호를 모두 입력해주세요.");
            return false;
        }

        if ( !rgex.test(email) ) {
            alertc("확인 요청", "이메일을 확인해주세요.");
            return false;
        }

        return true; // 폼 정상 제출
    }
</script>
@endsection
