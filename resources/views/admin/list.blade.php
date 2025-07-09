@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">
        <div class="mb-4 with3">
            <h2 class="text-2xl font-bold">회원 관리</h2>
            <button type="button" class="btn btn-dark" onclick="add_admin()">관리자 계정 만들기</button>
        </div>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" id="all" aria-current="page" href="{{ route('admin.list') }}">전체</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divY" href="{{ route('admin.list', ['div' => 'Y' ]) }}">활동 ({{ $sta['user_cnt'] ?? '' }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divN" href="{{ route('admin.list', ['div' => 'N' ]) }}">탈퇴</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divA" href="{{ route('admin.list', ['div' => 'A' ]) }}">관리자({{ $sta['admin_cnt'] ?? '' }})</a>
            </li>
        </ul>

        <table class="table table-striped table-sm">
            <thead>
                <tr class="text-center">
                    <th scope="col" class="w-10">아이디</th>
                    <th scope="col" class="w-10">이름</th>
                    <th scope="col" class="w-20">이메일</th>
                    <th scope="col" class="w-15">가입일</th>
                    <th scope="col" class="w-20">휴대폰</th>
                    <th scope="col" class="w-10">상태</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @forelse($users as $user)
                    <tr class="text-center" style="cursor:pointer;"scope="col" data-user-id="{{ $user->id }}" onclick="infoPopup(this)">
                        <td scope="col">{{ $user->id }}</td>
                        <td scope="col"><strong>{{ $user->name_r ?? '' }}</strong></td>
                        <td scope="col">{{ $user->email_r ?? '' }}</td>
                        <td scope="col">{{ $user->created_at }}</td>
                        <td scope="col">{{ $user->ph_r ?? '' }}</td>
                        <td scope="col">{{ $user->sta }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan=6 class="text-center">-</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 페이지네이션 -->
        <div class="mt-4 mb-4" id="pagination-wrapper">
            {{ $users->appends(request()->query())->links() }}
            <form id="searchForm" method="GET" action="{{ route('admin.list') }}">
                @csrf
                <input type="hidden" name="div" value="{{ $div ?? '' }}">
                <input type="hidden" name="keyword" value="{{ $keyword ?? '' }}">
                <div class="search-box pl30" id="sb">
                    <select name="search_div" class="form-select w-auto d-inline-block me-2">
                        <option value="id">아이디</option>
                        <option value="name">이름</option>
                        <option value="email">이메일</option>
                    </select>
                    <input type="text" id="search" name="search" style="padding-top: 1px;">
                    <button type="button" class="btn btn-primary btn-sm" style="margin-left: 10px;" onclick="admin_search()">검색</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    function add_admin()
    {
        window.open('/admin/add', 'addPopup', 'width=600,height=500');
    }


    function admin_search()
    {
        var search = $("#search").val();

        if( search === '' )
        {
            alertc('확인 요청', '검색어를 입력해주세요.');
            return false;
        }

        $("#searchForm").submit();
    }

    function infoPopup(val)
    {
        var id = val.dataset.userId;

        window.open('/admin/info?id='+id, 'addPopup', 'width=600,height=500');
    }

    document.addEventListener("DOMContentLoaded", function () 
    {
        const urlParams = new URLSearchParams(window.location.search);
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
@endsection