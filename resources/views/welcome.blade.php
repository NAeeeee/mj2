@extends('layouts.app2')
        
        <!-- Page Content-->
        @section('content')

        @auth
            @if (Auth::check())
                @php
                    $user = Auth::user();

                    if ($user->is_admin === 'Y') {
                        $s_div = 'Y';
                        $url = route('admin.list');
                        $title = 'User';
                        $class = 'bi bi-person-circle';
                    } else {
                        $s_div = 'N';
                        $url = route('profile.edit', ['id' => $user->id]);
                        $title = 'Info';
                        $class = 'bi bi-info-circle';
                    }
                @endphp
            @endif
        @endauth

        @if(request()->query('verified') == 1)
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                alertc('성공','이메일 인증이 완료되었습니다!','p');
            });

        </script>
        @endif

        <header class="py-5">
            <div class="container px-lg-5">
                <!-- <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
                    <div class="m-4 m-lg-4">
                        <h1 class="display-5 fw-bold">welcome, mj world >.<</h1>
                    </div>
                </div> -->
                @if( $s_div === 'Y' ) {{-- 관리자 --}}
                    <div class="row">

                        <!-- 활동 회원수 -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2" onclick="location.href='{{ route('admin.list', ['div' => 'Y']) }}'" style="cursor: pointer;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                활동 회원 수</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $state['user_cnt'] ?? '' }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 미답변 글 수 -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2" onclick="location.href='{{ route('boards.index', ['div' => 'X']) }}'" style="cursor: pointer;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">미답변 글
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $state['post_cnt'] ?? '' }}</div>
                                                </div>
                                                <!-- <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: {{ $percentage }}%" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100">{{ $percentage }}</div>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 오늘 신규 회원 수 -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                오늘 신규 회원 수</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $state['today_user_cnt'] ?? '' }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 오늘 신규 게시물 수 -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                오늘 신규 게시물 수</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $state['today_post_cnt'] ?? '' }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="row">
                        <div>
                            <p class="t-ver">
                                &nbsp<strong>공지사항</strong>
                            </p>
                            <table class="table table-striped mb-2">
                                <thead>
                                    <tr class="table-dark text-center">
                                        <th scope="col" class="w-5">번호</th>
                                        <th scope="col" class="w-10">항목</th>
                                        <th scope="col" class="w-60">제목</th>
                                        <th scope="col" class="w-10">작성일</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($notice as $nn)
                                        <tr onclick="location.href='{{ route('notice.show', $nn->no) }}'" style="cursor: pointer;">
                                            <td class="text-center" scope="col">{{ $nn->no }}</td>
                                            <td class="text-center" scope="col">{{ $nn->div }}</td>
                                            <td scope="col">{{ $nn->title }}</td>
                                            <td class="text-center" scope="col">{{ $nn->created_date  }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan=4 class="text-center">작성된 공지가 없습니다.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </header>
        
        <section class="pt-4 mt-5">
            <div class="container px-lg-5">
                <!-- Page Features-->
                <div class="row gx-lg-5">
                    <!-- info -->
                    <a class="col-lg-6 col-xxl-4 mb-5" href="{{ $url }}">
                        <div>
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="{{ $class }}"></i></div>
                                    <h2 class="fs-4 fw-bold">{{ $title }}</h2>
                                    <!-- <p class="mb-0">With Bootstrap 5, we've created a fresh new layout for this template!</p> -->
                                </div>
                            </div>
                        </div>
                    </a>

                    @php
                        if ($user->is_admin && $user->is_admin == 'Y') {
                            $url = url('/boards');
                        } else {
                            $url = url('/request?id=' . $user->id);
                        }
                    @endphp

                    <a class="col-lg-6 col-xxl-4 mb-5" href="{{ $url }}">
                        <div>
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                                    <h2 class="fs-4 fw-bold">Board</h2>
                                    <!-- <p class="mb-0">Board</p> -->
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- 
                    <div class="col-lg-6 col-xxl-4 mb-5">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-patch-check"></i></div>
                                <h2 class="fs-4 fw-bold">A name you trust</h2>
                                <p class="mb-0">Start Bootstrap has been the leader in free Bootstrap templates since 2013!</p>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </section>
        @endsection


