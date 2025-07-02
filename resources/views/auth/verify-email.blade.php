@extends('layouts.app2')

@section('content')
<section class="pt-4">
    <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    이메일 인증이 필요해요
                </div>

                <div class="card-body">
                    <p class="mb-3">
                        회원가입 시 입력한 이메일 주소로 인증 메일을 보냈어요.<br>
                        메일을 열고 인증 링크를 눌러주세요.
                    </p>
                    <p class="mb-3">
                        입력한 이메일 : {{ auth()->user()->email ?? '' }}
                    </p>

                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="with">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">
                                인증 메일 다시 보내기
                            </button>
                        </form>

                        <button type="button" class="btn btn-outline-primary" onclick="openEmailPopup()">이메일 수정</button>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 mb-5">
                <button type="button" class="btn btn-danger" onclick="logoutChk()">
                    로그아웃
                </button>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

        </div>
    </div>
    </div>
</section>
@endsection

@push('scripts')
<script>

@if(session('status') == 'verification-link-sent')
    document.addEventListener('DOMContentLoaded', function () {
            alertc('성공', '이메일 인증 링크가 전송되었습니다.','p');
    });
@endif

function logoutChk()
{
    document.getElementById('logout-form').submit();
}

function openEmailPopup()
{
    window.open('/email/update-form', 'emailPopup', 'width=600,height=450');
}

</script>
@endpush