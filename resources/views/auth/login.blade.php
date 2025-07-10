@extends('layouts.app2')

@section('content')
    <!-- Session Status -->
    <section class="mt-4">
        @if ($errors->any())
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    alertc("로그인 실패", "{{ $errors->first() }}");
                });
            </script>
        @endif

        
        <div class="flex-center">
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-body">
                        <a href="/" class="text-nowrap logo-img text-center d-block py-3">
                            <img src="/img/user.png" alt="" width="10%">
                        </a>
                        <p class="text-center">MJ</p>
                        <form id="loginForm" class="m-3" method="POST" action="{{ route('login') }}" onsubmit="return formChk()">
                            @csrf
                            <div class="login_box mb-3">
                                <div class="form-floating input-top">
                                    <input type="email" class="form-control" id="em" name="email" placeholder="Email">
                                    <label for="em">Email</label>
                                </div>
                                <div class="form-floating input-bottom">
                                    <input type="password" class="form-control" id="pw" name="password" placeholder="Password">
                                    <label for="pw">Password</label>
                                </div>
                            </div>
                                

                            <div class="login_pw_btn mb-4">
                                <a class="text-secondary fw-bold" href="{{ route('password.request') }}">Forgot Password ?</a>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fs-4 mb-4">Sign In</button>
                            <div class="flex-center">
                                <a class="text-primary fw-bold ms-2" href="{{ route('register') }}">Create an account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
<script>
    function formChk() 
    {
        let email = document.getElementById("em").value.trim();
        let pw = document.getElementById("pw").value.trim();
        let rgex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;;

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

