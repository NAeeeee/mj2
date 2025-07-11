@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">
        <div class="mb-5">
            <h2 class="text-2xl font-bold ">게시글 상세보기</h2>
            <strong>
                @if($post->user->status == 'N')
                    이 글은 탈퇴한 회원이 작성하였습니다.
                @else
                    @if( $post->save_status == 'N' )
                        회원에 의해 삭제된 글입니다.
                    @endif
                @endif
            </strong>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">{{ $post->title }}</h4>
                <h6 class="card-subtitle mb-2 text-muted">
                    @if($post->user->status == 'N')
                        작성자 : 탈퇴회원
                    @else
                        작성자 : {{ $post->user->name ?? '익명' }}
                    @endif 
                    | 작성일 : {{ $post->created_at }}
                    @if( $post->created_at != $post->updated_at && $post->status == 'A' )
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

        {{-- 글 상태가 '요청' 일때 --}}
        @if($post->status == 'A' && Auth::user()->is_admin == 'N')
        <div class="mb-4">          
            <a href="{{ route('request.edit', $post->no) }}" class="btn btn-primary" style="display: inline-block;">
                수정
            </a>

            <button type="button" class="btn btn-danger"
                onclick="confirmDelete('{{ route('request.delete', $post->no) }}', 'request')">
                삭제
            </button>
        </div>
        @endif

        
        <div id="d-with" class="mb-4">
        @if(auth()->user()->is_admin === 'Y')
            <a href="{{ route('boards.index') }}" class="btn btn-secondary">목록으로</a>
            @if($post->save_status == 'Y' && $post->status == 'B' && $post['user']->status == 'Y')
                <button type="button" class="btn btn-danger" onclick="replyDivForm()">관리자 답글달기</button>
            @endif
        @else
            <a href="{{ route('request.list') }}?id={{ $post->user['id'] }}" class="btn btn-secondary">목록으로</a>
            {{-- 글상태가 '고객확인완료'일때 --}}
            @if( $post->status == 'D' )
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        $("#d-with").addClass('with2');
                    });
                </script>
                @if( $post->user_ok == 'N' )
                <form id="confirmForm" action="{{ route('request.submit') }}" method="POST" >
                    @csrf
                    <input type="hidden" name="no" id="no" value="{{ $post->no }}">
                    <button type="button" class="btn btn-danger" onclick="okChk();">확인완료</button>
                </form>
                @else
                <a href="{{ route('request.create', ['id' => Auth::user()->id ]) }}" class="btn btn-danger">추가문의하기</a>
                @endif
            @endif
            {{-- 글상태가 '반려', '처리완료'일때 --}}
            @if( $post->status == 'E' || $post->status == 'Z' )
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        $("#d-with").addClass('with2');
                    });
                </script>
                <a href="{{ route('request.create', ['id' => Auth::user()->id ]) }}" class="btn btn-danger">추가문의하기</a>
            @endif
        @endif
        </div>


        {{-- 작성된 관리자 댓글 --}}
        @if(!empty($reply))
            <div class="card mb-5">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        관리자 답변
                    </h6>
                    <h6 class="card-subtitle mb-2 text-muted">
                        작성일: {{ $reply->created_at }}
                    </h6>
                    <p class="card-text mt-3">{!! nl2br(e($reply->content)) !!}</p>
                    @forelse ($img as $i)
                        @if( $i->target_type == 'R' )
                            <img src="{{ asset('storage/img/' . $i->pathDate . '/' . $i->savename) }}" width="200">
                        @endif
                    @empty
                                
                    @endforelse
                </div>
            </div>
        @endif

        

        {{-- 관리자 댓글 --}}
        <div class="form-floating mt-4" id="reply" style="display: none;">
            <form id="replyForm" action="{{ route('boards.reply') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="admin_id" name="admin_id" value="{{ Auth::user()->id }}">
                <input type="hidden" id="user_id" name="user_id" value="{{ $post->user_id }}">
                <input type="hidden" id="post_no" name="post_no" value="{{ $post->no }}">
                
                <div class="mb-2">
                    <textarea class="form-control" name="content" placeholder="Leave a comment here" id="content" style="height: 100px"></textarea>
                </div>

                <div class="mb-1">
                    <input class="form-control mb_5" type="file" id="file" name="file[]" multiple>
                    <input class="form-control mb_5" type="file" id="file" name="file[]" multiple>
                    <input class="form-control mb_5" type="file" id="file" name="file[]" multiple>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-primary" onclick="reply()">답글등록</a>
                </div>
            </form>
        </div>


    </div>
</section>
@endsection

@push('scripts')
<script>
    function replyDivForm()
    {
        $("#reply").show();
    }

    function reply()
    {
        var content = document.getElementById('content').value.trim();

        if( content === '' )
        {
            alertc('확인 요청', '내용을 입력해주세요.');
            return false;
        }

        document.getElementById('replyForm').submit();
    }

    function okChk()
    {
        alertc('확인 요청', '해당 게시물을 완료 처리 하시겠습니까?', 'p');

        $('#modal_btn').on('click', function () {
            $('#confirmForm').submit();
        });
    }
</script>
@endpush
