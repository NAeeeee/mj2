@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">

        <div class="mb-4 with3">
            <h2 class="text-2xl font-bold cp" onclick="location.href='{{ route('notice.index') }}'">공지사항</h2>
            @if( auth()->check() && auth()->user()->is_admin === 'Y' )
            <div>
                <a class="btn btn-dark" href="{{ route('notice.list') }}">공지 관리</a>
                <a class="btn btn-dark" href="{{ route('notice.create') }}">공지 작성</a>
            </div>
            @endif
        </div>

        <div>
            <p class="t-ver">
                Total <strong>{{ $page['totalCnt'] ?? '' }}</strong> / {{ $page['totalPages'] ?? 1 }} Page
            </p>
        </div>

        <table class="table table-striped mb-4">
             <thead>
                <tr class="table-dark text-center">
                    <th scope="col" class="w-5">번호</th>
                    <th scope="col" class="w-10">항목</th>
                    <th scope="col" class="w-70">제목</th>
                    <th scope="col" class="w-10">작성일</th>
                </tr>
            </thead>

            <tbody>
                @forelse($notice as $nn)
                    <tr onclick="location.href='{{ route('notice.show', $nn->no) }}'" style="cursor: pointer;">
                        <td class="text-center" scope="col">{{ $nn->no }}</td>
                        <td class="text-center" scope="col">{{ $nn->div }}</td>
                        <td scope="col">{{ $nn->title }}</td>
                        <td class="text-center" scope="col">{{ $nn->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan=4 class="text-center">작성된 공지가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        {{ $notice->appends(request()->query())->links() }}
        <div class="g-5 mb-3">
            <form id="searchForm" method="GET" action="{{ route('notice.index') }}" onsubmit="return searchSumbit(this);">
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
</section>
@endsection