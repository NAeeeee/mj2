@extends('layouts.app2')

@section('content')
    <section class="pt-3 mt-4">
    <div class="container px-lg-5">
        <div class="mb-5">
            <input type="hidden" name="div" id="div" value="{{ $div ?? '' }}">
            <h2 class="text-2xl font-bold">쪽지 목록</h1>
        </div>

        <!-- 쪽지탭 -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" id="recevie" href="/message/inbox?div=R">받은 쪽지함</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="send" href="/message/inbox?div=S">보낸 쪽지함</a>
            </li>
        </ul>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr class="text-center">
                    <th scope="col" class="w-10">구분</th>
                    <th scope="col" class="w-20">제목</th>
                    <th scope="col" class="w-10">발신자</th>
                    @if( $div == 'S' )<th scope="col" class="w-10">수신자</th>@endif
                    <th scope="col" class="w-20">전송일</th>
                    <th scope="col" class="w-10">읽음</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($message as $msg)
                    <tr>
                        <td class="text-center">{{ $msg->div === 'S' ? '보낸 쪽지' : '받은 쪽지' }}</td>
                        <td onclick="window.open('{{ route('message.show', $msg->no) }}', 'msgPopup', 'width=600,height=450'); return false;" style="cursor: pointer;">
                            <strong>{{ $msg->title }}</strong>
                        </td>
                        <td class="text-center">{{ $msg->name }}</td>
                        @if( $div == 'S' )
                        <td class="text-center">{{ $msg->name_r ?? '' }}</td>
                        @endif
                        <td class="text-center">{{ $msg->created_at }}</td>
                        <td class="text-center">
                            @if( $msg->is_read == 1 )
                                <img class="msg_chk" src="/img/chk_ok.png" width=18>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        @php
                            if( $div == 'S' )
                            $colspan = '6';
                            else
                            $colspan = '5';
                        @endphp 
                        <td colspan="{{ $colspan }}" class="text-center">메세지가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 페이지네이션 -->
        <div class="mt-4 mb-5">
            {{ $message->links() }}
        </div>
        
    </div>
    </section>
@endsection


@push('scripts')
<script>
    window.onload = function() {
        var div = $("#div").val();

        if( div == 'S' )
        {
            $("#send").addClass('active');
            $("#recevie").removeClass('active');
        }
        else
        {
            $("#recevie").addClass('active');
            $("#send").removeClass('active');
        }
    };
</script>
@endpush