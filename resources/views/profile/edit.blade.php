@extends('layouts.app2')

@section('content')

    <section class="pt-4 mt-5">
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
            <input type="text" class="form-control" id="id" value="{{ $user->id }}" disabled>
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
            <input type="text" class="form-control" value="{{ $user->ph }}">
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
            <button class="btn btn-danger fr" onclick="confirmDelete('{{ route('profile.destroy', $user->id) }}', 'GET')" >
                회원탈퇴
            </button>
            @endif
        </div>

        @if($user->is_admin == 'N')
        <div class="input-group mb-4">
            <table class="table table-striped">
                <thead>
                    <tr class="table-dark">
                        <th scope="col" class="w-20">제목</th>
                        <th scope="col" class="w-10">항목</th>
                        <th scope="col" class="w-15">작성일</th>
                        <th scope="col" class="w-10">상태</th>
                        <th scope="col" class="w-10">관리</th>
                    </tr>
                </thead>
                <tbody>
                @if(!empty($board) && count($board) > 0)
                    @foreach ($board as $post)
                        <tr>
                            <td scope="col">
                                <a href="{{ route('boards.show', $post->no) }}" class="text-blue-600 hover:underline">
                                    {{ $post->title }}
                                </a>
                            </td>
                            <td scope="col">{{ $post->div }}</td>
                            <td scope="col">{{ $post->updated_at }}</td>
                            <td scope="col">{{ $post->status }}</td>
                            <td scope="col">
                            @if($post->sta == 'A')
                            <!-- 삭제 버튼 (form으로 감싸기) -->
                            <form action="{{ route('request.delete', $post->no) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">삭제</button>
                            </form>
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