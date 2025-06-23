@extends('layouts.app')

@section('content')
<div class="container">
    <h2>회원 목록</h2>
    <table class="table">
        <thead>
            <tr>
                <th>이름</th>
                <th>이메일</th>
                <th>가입일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                <td>
                    <form action="" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('삭제할까요?')">삭제</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">회원이 없습니다.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection