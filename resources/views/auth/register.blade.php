@extends('layouts.app2')

@section('content')
<section class="pt-4">
    <div class="container px-lg-5" id="all-section">
    <form id="form" method="POST" action="{{ route('register') }}" class="mt-4">
        @csrf

        <!-- Name -->
        <div class="row mb-3">
            <label for="name" class="form-label">Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" oninput="onlyStr(this)" maxlength=20 required>
            </div>
            @error('name')
                <p class="mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="row mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="email" name="email" oninput="onlyEmail(this);" required>
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div class="row mb-3">
            <label for="ph" class="form-label">Phone</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="ph" name="ph" oninput="onlyNumber(this);" maxlength=11 required>
            </div>
            @error('ph')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Password -->
        <div class="row mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="col-sm-10">
                <input type="password" id="password" name="password" class="form-control" aria-describedby="passwordHelpInline" required>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!--Confirm Password -->
        <div class="row mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="col-sm-10">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" aria-describedby="passwordHelpInline" required>
            </div>
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>


        <div class="flex items-center justify-end mt-4 mb-5">
            <button type="button" class="btn btn-primary" onclick="chk()">
                {{ __('Register') }}
            </button>
        </div>
    </form>
    </div>
 
    <div id="loading-section" style="display:none;">
        <div class="d-flex justify-content-center align-items-center" style="height: 390px;">
            <div class="spinner-border text-secondary" role="status" style="width: 4rem; height: 4rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>

function chk()
{
    var name = document.getElementById('name').value;
    var email = document.getElementById('email').value;
    var ph = document.getElementById('ph').value;
    var pw = document.getElementById('password').value;
    var pw2 = document.getElementById('password_confirmation').value;

    if( name == '' )
    {
        alertc('확인 요청','이름을 입력해주세요.');
        return false;
    }
    else if( email == '')
    {
        alertc('확인 요청','이메일을 입력해주세요.');
        return false;
    }
    else if( ph == '' )
    {
        alertc('확인 요청','휴대폰 번호를 입력해주세요.');
        return false;
    }
    else if( ph.length < 10)
    {
        alertc('확인 요청','휴대폰 번호를 확인해주세요.');
        return false;
    }
    else if ( pw.length < 8 ) {
        alertc('확인 요청','비밀번호는 최소 8자 이상이어야 합니다.');
        return false;
    }
    else if( pw == '' || pw2 == '' )
    {
        alertc('확인 요청','비밀번호를 입력해주세요.');
        return false;
    }

    if (pw !== pw2 ) {
        alertc('확인 요청','비밀번호가 일치하지 않습니다.');
        return false;
    }

    var rgex = /[!@#$%^&*(),.?":{}|<>]/;
    var emailrgex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    var all = document.getElementById('all-section');
    var ls = document.getElementById('loading-section');

    if (!rgex.test(pw)) {
        alertc('확인 요청',"비밀번호에 특수문자를 최소 1개 이상 포함해야 합니다.");
        return false;
    }

    if ( !emailrgex.test(email) ) {
        alertc("확인 요청", "이메일을 확인해주세요.");
        return false;
    }

    all.style.display = 'none';
    ls.style.display = 'block';
    
    document.getElementById('form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('name');

    input.addEventListener('input', () => {
        input.value = input.value.toLowerCase();
    });
});
</script>
@endpush
