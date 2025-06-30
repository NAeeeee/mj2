@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">
        <div class="mb-4-5">
            <input type="hidden" name="div" id="div" value="{{ $div ?? '' }}">
            <h2 class="text-2xl font-bold">게시판 목록(관리자화면)</h2>
        </div>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" id="all" href="/boards">전체</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divX" href="/boards?div=X">미답변</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divO" href="/boards?div=O">답변완료</a>
            </li>

            {{-- 항목 --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">항목</a>
                <ul class="dropdown-menu">
                <li><a class="dropdown-item" id="divA" href="/boards?div=A">견적</a></li>
                <li><a class="dropdown-item" id="divB" href="/boards?div=B">배송</a></li>
                <li><a class="dropdown-item" id="divC" href="/boards?div=C">계정</a></li>
                <li><a class="dropdown-item" id="divD" href="/boards?div=D">기타</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="divE" href="/boards?div=E">반려</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divZ" href="/boards?div=Z">처리완료</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divdel" href="/boards?div=del">삭제</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divdelu" href="/boards?div=delu">탈퇴</a>
            </li>
        </ul>

        @php
            // 답변 달린 글이 하나라도 있는지 확인
            $hasComment = false;
            foreach ($posts as $post) {
                if (!empty($post->reply_at)) {
                    $hasComment = true;
                    break;
                }
            }
        @endphp

        <!-- 테이블 -->
        <table class="table table-bordered table-hover align-middle" style="font-size:14px">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="w-5">번호</th>
                    <th scope="col" class="w-10">항목</th>
                    <th scope="col" class="w-25">제목</th>
                    <th scope="col" class="w-10">작성자</th>
                    <th scope="col" class="w-15">작성일</th>
                    @if( $div != 'X' && $hasComment )<th scope="col" class="w-15">댓글</th> @endif
                    <th scope="col" class="w-15">상태</th>
                    @if( $div === 'O' || $div === 'X' )<th scope="col" class="w-15">관리</th>@endif
                </tr>
            </thead>
            <tbody id="post-table-body">
                @forelse ($posts as $post)
                    <tr>
                        <th scope="col">{{ $post->no }}</th>
                        <td>{{ $post->div }}</td>
                        <td>
                            <a href="{{ route('request.show', $post->no) }}" class="text-decoration-none text-primary">
                                {{ $post->title }}
                            </a>
                        </td>
                        <td>
                            @if($post->users_status == 'N')
                                탈퇴회원
                            @else
                                {{ $post->name ?? '익명' }}
                            @endif
                        </td>
                        <td>{{ $post->created_at }}</td>
                        @if( $div != 'X' && $hasComment )
                        <td>
                            {{ $post->reply_at ?? '' }}
                        </td>
                        @endif
                        <td>
                            @if($post->save_status == 'N') <!-- 삭제된 글 -->
                                <div class="status_div" id="user_del">
                                    <img class="del-zone" src="/img/user_del2.png" width="18.5">
                                    사용자 삭제 게시물
                                </div>
                            @elseif($post->users_status == 'N') <!-- 탈퇴한 회원의 글 -->
                                <div class="status_div" id="user_del2">
                                    <img class="del-zone" src="/img/user_del2.png" width="18.5">
                                    탈퇴 회원 게시물
                                </div>
                            @else
                                {{--
                                    미답변일때 A,B
                                    상태탭 A 요청접수 B 담당자 확인중
                                    관리탭 A 빈칸 B 미답변표시

                                    답변완료 C,D
                                    상태탭 C 담당자답변완료 D 고객확인완료
                                    관리탭 C 빈칸 D 상태변경 셀렉트박스(반려, 답변완료)
                                --}}
                                {{-- 요청 --}}
                                {{-- 상태가 '요청 접수' 관리자 열람,답글 X --}}
                                @if( $post->status == 'A' )
                                    <div class="status_div" id="chk_no">
                                        <img class="chk-zone" src="/img/chk.png" width="18.5" >
                                        요청접수
                                    </div>
                                {{-- 검토 --}}
                                {{-- 상태가 '담당자 확인 중'이고 답글이 달리지 않음 --}}
                                @elseif( $post->status == 'B' && $post->reply_at == '' )
                                    <div class="status_div" id="chk_no">
                                        <img class="chk-zone" src="/img/chk.png" width="18.5">
                                        담당자 확인중
                                    </div>
                                {{-- 처리 --}}
                                {{-- 상태 '담당자 답변 완료' 달린 답글 고객 확인 완료 --}}
                                @elseif($post->status == 'C')
                                    <div class="status_div" id="user_chk_ok">
                                        <img class="chk-zone" src="/img/chk.png" width="18.5">
                                        답변 완료
                                    </div>
                                {{-- 상태 '담당자 답변 완료' 달린 답글 고객 확인 완료 --}}
                                @elseif($post->status == 'D')
                                    <div class="status_div_p" id="user_chk_ok">
                                        <img class="chk-zone" src="/img/chk_ok.png" width="18.5">
                                        고객 확인 완료
                                    </div>
                                {{-- 상태 '반려' 처리불가 / 반려 --}}
                                @elseif($post->status == 'E')
                                    <div class="status_div" id="user_chk_ok">
                                        <img class="del-zone" src="img/user_del2.png" width="22">
                                        반려
                                    </div>
                                {{-- 상태 '완료' 처리 완료 --}}
                                @elseif($post->status == 'Z')
                                    <div class="status_div_p" id="user_chk_ok">
                                        <img class="chk-zone" src="/img/chk_ok.png" width="18.5">
                                        처리 완료
                                    </div>
                                @else

                                @endif
                            @endif
                        </td>
                        {{-- 미답변, 답변완료 탭 --}}
                        @if( $div === 'O' || $div === 'X' )
                        <td>
                            @if( $post->status == 'A' || $post->status == 'C' )

                            @elseif( $post->status == 'B' && $post->reply_at == '' )
                            <div class="form-text-del" id="chk_no">
                                <img class="chk-zone" src="/img/chk_no2.png" width="18.5">
                                미답변
                            </div>
                            @elseif($post->status == 'D')
                            <form action="{{ route('boards.updateStatus', $post->no) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select name="status" class="form-select form-select-sm w-auto d-inline-block me-2">
                                    <option value="E" {{ old('status', $post->status) === 'E' ? 'selected' : '' }}>반려</option>
                                    @if( $post->view_status == 'Z' )
                                    <option value="Z" {{ old('status', $post->status) === 'Z' ? 'selected' : '' }}>완료</option>
                                    @endif
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm mb_5">상태 변경</button>
                            </form>
                            @endif
                        </td>
                        @endif
                    </tr>
                @empty
                <tr>
                    <td colspan=6 class="text-center">작성한 글이 없습니다.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 페이지네이션 -->
        <div class="mt-4 aaa mb-4" id="pagination-wrapper">
            {{ $posts->appends(request()->query())->links() }}
            <form id="searchForm" method="GET" action="{{ route('boards.index') }}">
                @csrf
                <input type="hidden" name="div" value="{{ $div ?? '' }}">
                <input type="hidden" name="keyword" value="{{ $keyword ?? '' }}">
                <div class="search-box pl30" id="sb">
                    <select name="search_div" class="form-select w-auto d-inline-block me-2">
                        <option value="title">제목</option>
                        <option value="content">내용</option>
                        <option value="user_id">작성자</option>
                    </select>
                    <input type="text" id="search" name="search" style="padding-top: 1px;">
                    <button type="button" class="btn btn-primary btn-sm" style="margin-left: 10px;" onclick="qwer()">검색</button>
                </div>
            </form>
        </div>
    </div>


<style>
    .aaa
    {
        display: flex;
    }
    .search-box
    {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .pl30
    {
        padding-left: 30px;
    }
</style>

</section>

@endsection

@push('scripts')
<script>
    function qwer()
    {
        var f = $("#searchForm");
        f.submit();
    }

    $(document).ready(function () {
        if ($('ul.pagination').length > 0) {
            $('#sb').addClass('pl30');
        } else {
            $('#sb').removeClass('pl30');
        }
    });

    
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        // URL에서 div 값 가져옴
        const div = urlParams.get('div');

        // div 값이 없으면 all 있으면 해당 div 탭 선택
        const activeTabId = div ? 'div' + div : 'all';

        // 모든 탭에서 active 제거
        const navLinks = document.querySelectorAll('.nav-link, .dropdown-item');
        navLinks.forEach(link => link.classList.remove('active'));

        // 선택한 값 id로 선택
        const activeTab = document.getElementById(activeTabId);
        if (activeTab) 
        {
            // active 클래스 add
            activeTab.classList.add('active');

            const category = activeTab.closest('.dropdown-menu'); // 항목 (ul)
            if (category) 
            {
                // 항목(ul) 앞 항목 가져오기
                const chk = category.previousElementSibling;
                if (chk && chk.classList.contains('nav-link')) {
                    chk.classList.add('active');
                }
            }
        }

        @if(session('msg_p2'))
            alertc("{{ session('title_d') }}","{{ session('msg_p2') }}",'p');
            setTimeout(() => {
                    location.reload();
                }, 18.500);
        @endif
    });

</script>

@endpush
