@extends('layouts.app')

@section('content')
<div class="mt-5">
    <!-- <div class="with mb-4">
        <h1 class="text-2xl font-bold">쪽지 상세보기</h1>
        <a href="{{ url('/') }}">
            <img src="/img/home.png" width="30px" style="align-content: center;float:right;">
        </a>
    </div> -->

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">{{ $message->title }}</h4>
            <h6 class="card-subtitle mb-2 text-muted">
                보낸이 : {{ $message->sender_id }} |
                시간 : {{ $message->created_at->format('Y-m-d H:i') }}
            </h6>
            <p class="card-text mt-5">{{ $message->content }}</p>
            <div class="mt-5">
                <a href="{{ route('boards.show', $message->post_no) }}" target="_blank" class="btn btn-primary">원글 이동</a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>

</script>
@endpush
