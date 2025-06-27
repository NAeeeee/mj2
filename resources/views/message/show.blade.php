@extends('layouts.popup')

@section('content')
<div class="container mt-5">
    <div class="with mb-4">
        <h3 class="text-2xl font-bold">쪽지 상세보기</h3>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">{{ $message->title }}</h4>
            <h6 class="card-subtitle mb-2 text-muted">
                발신자 : {{ $message->name }} |
                시간 : {{ $message->created_at->format('Y-m-d H:i') }}
            </h6>
            <p class="card-text mt-5">{!! $message->content !!}</p>
            <div class="mt-5">
                @auth
                    @php
                        if ( isset($message->status) && $message->status && auth()->user()->is_admin === 'Y' ){
                            $url = route('boards.index', ['div' => 'O']);
                            $comment = '리스트 확인';
                        }
                        else {
                            $url = route('request.show', ['id' => $message->post_no]);
                            $comment = '원글 이동';
                        }
                    @endphp
                @endauth
                <a href="{{ $url }}" target="_blank" class="btn btn-primary">{{ $comment }}</a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>

</script>
@endpush
