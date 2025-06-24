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
                <a class="nav-link" id="all" aria-current="page" href="/boards">전체</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divX" href="/boards?div=X">미답변</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divO" href="/boards?div=O">답변완료</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divA" href="/boards?div=A">견적</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divB" href="/boards?div=B">배송</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divC" href="/boards?div=C">계정</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divD" href="/boards?div=D">기타</a>
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
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="w-10">번호</th>
                    <th scope="col" class="w-10">항목</th>
                    <th scope="col" class="w-25">제목</th>
                    <th scope="col" class="w-10">작성자</th>
                    <th scope="col" class="w-15">작성일</th>
                    @if( $div != 'X' && $hasComment )<th scope="col" class="w-15">댓글</th> @endif
                    <th scope="col" class="w-15">관리</th>
                </tr>
            </thead>
            <tbody id="post-table-body">
                @foreach ($posts as $post)
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
                                <div class="user-del" id="user_del">
                                    <img class="del-zone" src="/img/user_del2.png" width="22">
                                    사용자 삭제 게시물
                                </div>
                            @elseif($post->users_status == 'N') <!-- 탈퇴한 회원의 글 -->
                                <div class="user-del" id="user_del2">
                                    <img class="del-zone" src="/img/user_del2.png" width="22">
                                    탈퇴 회원 게시물
                                </div>
                            @else
                                @if($post->status == 'D')
                                    <div class="user-chk-ok" id="user_chk_ok">
                                        <img class="chk-zone" src="/img/chk_ok.png" width="18.5">
                                        고객 확인 완료
                                        <!-- <img src="/img/chk_ok.png" width="18"> ✔ -->
                                    </div>
                                {{-- 상태가 '검토'이고 답글이 달리지 않은것 --}}
                                @elseif( $post->status == 'B' && $post->reply_at == '' )
                                    <div class="form-text-del" id="chk_no">
                                        <img class="no-chk-zone" src="/img/chk_no2.png" width="20">
                                        미답변
                                    </div>
                                @else
                                    <form action="{{ route('boards.updateStatus', $post->no) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select w-auto d-inline-block me-2">
                                            <option value="A" {{ old('status', $post->status) === 'A' ? 'selected' : '' }}>요청</option>
                                            <option value="B" {{ old('status', $post->status) === 'B' ? 'selected' : '' }}>검토</option>
                                            <option value="C" {{ old('status', $post->status) === 'C' ? 'selected' : '' }}>처리</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm mb_5">상태 변경</button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
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
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => link.classList.remove('active'));

        // 해당 div에 해당하는 id를 가진 탭에 active 추가
        const activeTab = document.getElementById(activeTabId);
        if (activeTab) {
            activeTab.classList.add('active');
        }
    });
</script>

@endpush
