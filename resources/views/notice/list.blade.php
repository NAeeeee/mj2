@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">
        <div class="mb-4-5 with3">
            <h2 class="text-2xl font-bold">공지 관리</h2>
            <a href="{{ route('notice.create', ['id' => Auth::user()->id ]) }}" class="btn btn-dark" style="float:right">
                공지 작성
            </a>
        </div>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" id="all" href="{{ route('notice.list') }}">전체({{ $sta['all_cnt'] ?? '' }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divY" href="{{ route('notice.list', ['div' => 'Y']) }}">노출({{ $sta['y_cnt'] ?? '' }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="divN" href="{{ route('notice.list', ['div' => 'N']) }}">비노출({{ $sta['n_cnt'] ?? '' }})</a>
            </li>
        </ul>

        <table class="table table-striped table-sm">
            <thead>
                <tr class="table-dark text-center">
                    <th scope="col" class="w-5">번호</th>
                    <th scope="col" class="w-10">항목</th>
                    <th scope="col" class="w-30">제목</th>
                    <th scope="col" class="w-15">작성일</th>
                    <th scope="col" class="w-10">작성자</th>
                    <th scope="col" class="w-20">상태</th>
                    <th scope="col" class="w-10">관리</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($notice) && count($notice) > 0)
                    @foreach ($notice as $nn)
                        <tr onclick="location.href='{{ route('notice.edit', $nn->no) }}'" style="cursor: pointer;">
                            <td scope="col" class="text-center">{{ $nn->no }}</td>
                            <td scope="col" class="text-center">{{ $nn->div }}</td>
                            <td scope="col"><strong>{{ $nn->title }}</strong></td>
                            <td scope="col" class="text-center">{{ $nn->created_at }}</td>
                            <td scope="col" class="text-center">{{ $nn->save_name }}</td>
                            <td scope="col" class="text-center">{{ $nn->ss }}</td>
                            <td scope="col" class="text-center">{{ $nn->iv }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan=6 class="text-center">작성한 공지가 없습니다.</td>
                    </tr>
                @endif
            </tbody>
        </table>


        {{ $notice->appends(request()->query())->links() }}
        <div class="g-5 mb-3">
            <form id="searchForm" method="GET" action="{{ route('notice.list') }}" onsubmit="return searchSumbit(this);">
                <div class="row g-3">    
                    @csrf
                    <input type="hidden" name="div" value="{{ $div ?? '' }}">
                    <input type="hidden" name="keyword" value="{{ $keyword ?? '' }}">
                    <div class="col-md-1-5">
                        <select name="search_div" class="form-select">
                            <option value="title">제목</option>
                            <option value="content">내용</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="search" name="search">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn">검색</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


<script>
function addUpload() 
{
    var form = document.getElementById('hiddenForm2');
    form.submit();
}

    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);

        const div = urlParams.get('div');

        const activeTabId = div ? 'div' + div : 'all';

        const navLinks = document.querySelectorAll('.nav-link, .dropdown-item');
        navLinks.forEach(link => link.classList.remove('active'));

        const activeTab = document.getElementById(activeTabId);
        if (activeTab) 
        {
            activeTab.classList.add('active');
        }

        @if(session('msg_p2'))
            alertc("{{ session('title_d') }}","{{ session('msg_p2') }}",'p');
            setTimeout(() => {
                    location.reload();
                }, 18.500);
        @endif
    });

</script>

</section>
@endsection