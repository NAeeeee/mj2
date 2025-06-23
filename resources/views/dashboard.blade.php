@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (Auth::check()) <!-- 로그인된 유저만 확인 -->
                        @php
                            $user = Auth::user();  // 로그인된 사용자 정보 가져오기
                        @endphp

                        @if ($user->is_admin && $user->is_admin == 'Y')
                            <p>관리자 페이지로 가기</p>
                            <a href="/admin/list">관리자 대시보드</a>
                        @else
                            {{ __("You're logged in!") }}
                            <p>메인 페이지로 가기</p>
                            <a href="/">대시보드</a>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
