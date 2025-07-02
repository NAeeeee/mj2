@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">
        <div class="mb-5">
            <h2 class="text-2xl font-bold">공지사항 상세보기</h2>
        </div>

        <div class="card mb-4-5">
            <div class="card-body">
                <h4 class="card-title">{{ $notice->title }}</h4>
                <h6 class="card-subtitle mb-2 text-muted">
                    관리자 | 작성일 : {{ $notice->created_at }}
                </h6>
                <p class="card-text mt-3">{{ $notice->content }}</p>
                @forelse ($img as $i)
                    @if( $i->target_type == 'N' )
                        <img src="{{ asset('img/' . $i->pathDate . '/' . $i->savename) }}" width="200">
                    @endif
                @empty
                    
                @endforelse
                
            </div>
        </div>

        @if( auth()->check() && Auth::user()->is_admin === 'Y' )
        <div class="mb-4">
            <form action="{{ route('notice.edit', $notice->no) }}"
                method="POST"
                style="display: inline-block;">
                @csrf
                <button type="submit" class="btn btn-primary">수정</button>
            </form>

            <form action="{{ route('notice.delete', $notice->no) }}"
                method="POST"
                style="display: inline-block;"
                onsubmit="return confirm('정말 삭제하시겠습니까?');">
                @csrf
                <button type="submit" class="btn btn-danger">삭제</button>
            </form>
        </div>
        @endif

        <div class="mb-4-5">
            <a href="{{ route('notice.index') }}" class="btn btn-secondary">목록으로</a>
        </div>


    </div>

<script>

</script>

</section>
@endsection
