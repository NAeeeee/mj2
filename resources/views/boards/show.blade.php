@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">
        <div class="mb-5">
            <h2 class="text-2xl font-bold ">게시글 상세보기</h2>
        </div>

        <div class="card mb-4-5">
            <div class="card-body">
                <h4 class="card-title">{{ $post->title }}</h4>
                <h6 class="card-subtitle mb-2 text-muted">
                    @if($post->user->status == 'N')
                        작성자 : 탈퇴회원
                    @else
                        작성자 : {{ $post->user->name ?? '익명' }}
                    @endif 
                    | 작성일 : {{ $post->created_at }}
                    @if( $post->created_at != $post->updated_at )
                    | 수정일 : {{ $post->updated_at }}
                    @endif
                </h6>
                <p class="card-text mt-3">{!! nl2br(e($post->content)) !!}</p>
                @forelse ($img as $i)
                    @if( $i->target_type == 'P' )
                        <img src="{{ asset('storage/img/' . $i->pathDate . '/' . $i->savename) }}" width="200">
                    @endif
                @empty
                    
                @endforelse
                
            </div>
        </div>

        @if($post->status == 'A' && Auth::user()->is_admin == 'N')
        <div class="mb-4">
            <!-- <a href="{{ route('boards.edit', $post->no) }}" class="btn btn-primary">수정</a> -->
            <form action="{{ route('boards.edit', $post->no) }}"
                method="POST"
                style="display: inline-block;">
                @csrf
                <button type="submit" class="btn btn-primary">수정</button>
            </form>

            <form action="{{ route('boards.delete', $post->no) }}"
                method="POST"
                style="display: inline-block;"
                onsubmit="return confirm('정말 삭제하시겠습니까?');">
                @csrf
                <button type="submit" class="btn btn-danger">삭제</button>
            </form>
        </div>
        @endif

        <div class="mb-4-5">
        @if(auth()->user()->is_admin == 'Y')
            <a href="{{ route('boards.index') }}" class="btn btn-secondary">목록으로</a>
            @if($post->save_status == 'Y' && $post->status == 'B')
                <button type="button" class="btn btn-danger" onclick="div()">관리자 답글달기</button>
            @endif
        @else
            <a href="{{ route('request.list') }}?id={{ $post->user['id'] }}" class="btn btn-secondary">목록으로</a>
        @endif
        </div>


        @if(!empty($reply))
            <div class="card mb-5">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        관리자 답변
                    </h6>
                    <h6 class="card-subtitle mb-2 text-muted">
                        작성일: {{ $reply->created_at }}
                    </h6>
                    <p class="card-text mt-3">{{ $reply->content }}</p>
                    @forelse ($img as $i)
                        @if( $i->target_type == 'R' )
                            <img src="{{ asset('storage/img/' . $i->pathDate . '/' . $i->savename) }}" width="200">
                        @endif
                    @empty
                                
                    @endforelse
                </div>
            </div>
        @endif

        

    <div class="form-floating mt-4" id="reply" style="display: none;">
        <form id="replyForm" action="{{ route('boards.reply') }}" method="POST" class="row g-3" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="admin_id" name="admin_id" value="{{ Auth::user()->id }}">
            <input type="hidden" id="user_id" name="user_id" value="{{ $post->user_id }}">
            <input type="hidden" id="post_no" name="post_no" value="{{ $post->no }}">
            
            <div class="mb-2">
                <textarea class="form-control" name="content" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
            </div>

            <div class="mb-1">
                <input class="form-control mb_5" type="file" id="file" name="file[]" multiple>
                <input class="form-control mb_5" type="file" id="file" name="file[]" multiple>
                <input class="form-control mb_5" type="file" id="file" name="file[]" multiple>
            </div>

            <!-- <div class="col-md-2">
                <select class="form-select" name="status" value="{{ old('status', $post->status) }}">
                    <option value="A" {{ old('status', $post->status ?? '') == 'A' ? 'selected' : '' }}>{{ $post->sta['A'] }}</option>
                    <option value="B" {{ old('status', $post->status ?? '') == 'B' ? 'selected' : '' }}>{{ $post->sta['B'] }}</option>
                    <option value="C" {{ old('status', $post->status ?? '') == 'C' ? 'selected' : '' }}>{{ $post->sta['C'] }}</option>
                    <option value="D" {{ old('status', $post->status ?? '') == 'D' ? 'selected' : '' }}>{{ $post->sta['D'] }}</option>
                    <option value="E" {{ old('status', $post->status ?? '') == 'E' ? 'selected' : '' }}>{{ $post->sta['E'] }}</option>
                    <option value="Z" {{ old('status', $post->status ?? '') == 'Z' ? 'selected' : '' }}>{{ $post->sta['Z'] }}</option>
                </select>
            </div> -->
            <div class="col-md-2 mb-3">
                <!-- <label for="floatingTextarea2">Comments</label> -->
                <button type="button" class="mt-2 btn btn-primary" onclick="reply()">답글등록</a>
            </div>
        </form>
    </div>


    </div>
        </div>

<script>
    function div()
    {
        $("#reply").show();
    }

    function reply()
    {
        document.getElementById('replyForm').submit();
    }
</script>

</section>
@endsection
