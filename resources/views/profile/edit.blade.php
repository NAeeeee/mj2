@extends('layouts.app2')

@section('content')

    <section class="pt-4 mt-4">
    <div class="container px-lg-5">
        <!-- <div class="with mb-4">
            <h2 class="text-2xl font-bold">내정보</h1>
            <a href="{{ url('/') }}">
                <img src="/img/home.png" width="30px" style="align-content: center;float:right;">
            </a>
        </div> -->

        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">
                <img src="/img/user.png" width="18">
            </span>
            <input type="text" class="form-control" id="id" value="{{ $user->name }}" disabled>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">
                <img src="/img/email.png" width="18">
            </span>
            <input type="text" class="form-control" value="{{ $user->email }}" disabled>
        </div>

        <!-- 비밀번호 -->
        <div class="input-group mb-3" onclick="onChange()">
            <span class="input-group-text" id="basic-addon1">
                <img src="/img/key.png" width="18">
            </span>
            <input type="password" class="form-control" value="********" disabled>
        </div>

        <!-- 핸드폰번호 -->
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">
                <img src="/img/ph.png" width="18">
            </span>
            <input type="text" class="form-control" value="{{ $user->ph }}" disabled>
        </div>

        <div class="input-group mb-4">
            <span class="input-group-text" id="basic-addon1">
                <img src="/img/calendar.png" width="18">
            </span>
            <input type="text" name="created_date" class="form-control" value="{{ $user->created_date }}" disabled>
        </div>

        <div class="mb-4">
            <button type="button" class="btn btn-primary" onclick="popup({{ $user->id }})">
                비밀번호 변경
            </button>
            <button type="button" class="btn btn-primary" onclick="phPopup({{ $user->id }})">
                핸드폰 번호 변경
            </button>
            @if($user->is_admin == 'N')
            <button class="btn btn-danger fr" onclick="confirmWithdraw('{{ route('profile.destroy', $user->id) }}', 'GET')" >
                회원탈퇴
            </button>
            @endif
        </div>

        @if($user->is_admin == 'N')
        <div class="input-group mb-3">
            <table class="table table-striped">
                <thead>
                    <tr class="table-dark text-center">
                        <th scope="col" class="w-7">항목</th>
                        <th scope="col" class="w-50">제목</th>
                        <th scope="col" class="w-15">작성일</th>
                        <th scope="col" class="w-20">상태</th>
                        <th scope="col" class="w-10">관리</th>
                    </tr>
                </thead>
                <tbody>
                @if(!empty($board) && count($board) > 0)
                    @foreach ($board as $post)
                        <tr>
                            <td class="text-center" scope="col">{{ $post->div }}</td>
                            <td sclass="text-center" scope="col" onclick="location.href='{{ route('request.show', $post->no) }}'" style="cursor: pointer;">
                                <strong>{{ $post->title }}</strong>
                            </td>
                            <td class="text-center" scope="col">{{ $post->updated_at }}</td>
                            <td class="text-center" scope="col">{{ $post->status }}</td>
                            <td class="text-center" scope="col">
                            @if($post->sta == 'A')
                            <!-- 삭제 버튼 (form으로 감싸기) -->
                            <button type="button" class="btn btn-sm btn-danger" 
                                onclick="confirmDelete('{{ route('request.delete', $post->no) }}')">
                                삭제
                            </button>
                            @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan=5 class="text-center">작성한 글이 없습니다.</td>
                    </tr>
                @endif
            </table>
        </div>
        @endif
        
        {{-- 게시물 리스트에서 삭제 완료 팝업 --}}
        @if(session('success'))
        <div id="success-alert" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    var alert = document.getElementById('success-alert');
                    if (alert) {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    }
                }, 3000);
            });
        </script>
        @endif

    </div>

    <!-- 탈퇴 확인 모달 -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="withdrawForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">회원 탈퇴 확인</h5>
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
</section>
@endsection

@push('scripts')
<script>

// 비밀번호 변경 팝업
function popup(val)
{
    if( val == '' )
    {
        location.reload();
        return false;
    }
    window.open('/pwChange?val='+val, 'msgPopup', 'width=600,height=450'); 
    return false;
}

function phPopup(val)
{
    if( val == '' )
    {
        location.reload();
        return false;
    }
    window.open('/phChange?val='+val+'&div=ph', 'msgPopup2', 'width=600,height=450'); 
    return false;
}

</script>
@endpush