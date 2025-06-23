@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">
        <div class="mb-4-5 with3">
            <h2 class="text-2xl font-bold">견적 요청 목록</h2>
            <a href="{{ route('request.create', ['id' => Auth::user()->id ]) }}" class="btn btn-dark" style="float:right">
                @if(!empty($posts) && count($posts) > 0) 추가 @endif 견적 신청
            </a>
        </div>

        <table class="table table-hover">
            <thead>
                <tr class="table-active">
                    <th scope="col" class="w-10">번호</th>
                    <th scope="col" class="w-10">항목</th>
                    <th scope="col" class="w-20">제목</th>
                    <th scope="col" class="w-15">작성일</th>
                    <th scope="col" class="w-20">상태</th>
                    <th scope="col" class="w-10">관리</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($posts) && count($posts) > 0)
                    @foreach ($posts as $post)
                        <tr>
                            <td scope="col">{{ $post->no }}</td>
                            <td scope="col">{{ $post->div }}</td>
                            <td scope="col">
                                <a href="{{ route('boards.show', $post->no) }}" class="text-blue-600 hover:underline">
                                    {{ $post->title }}
                                </a>
                            </td>
                            <td scope="col">{{ $post->updated_at }}</td>
                            <td scope="col">{{ $post->status }}</td>
                            <td scope="col">
                            @if($post->sta == 'A')
                            <!-- 삭제 버튼 (form으로 감싸기) -->
                            <!-- <form action="{{ route('request.delete', $post->no) }}" method="POST" class="flex-center" onclick="delChk()">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">삭제</button>
                            </form> -->
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete('{{ route('request.delete', $post->no) }}')">
                                삭제
                            </button>
                            @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan=6 class="text-center">작성한 글이 없습니다.</td>
                    </tr>
                @endif
            </tbody>
        </table>

    <!-- 페이지네이션 -->
        <div class="mt-4 mb-4">
            {{ $posts->links() }}
        </div>
    
    </div>


<script>
function addUpload() 
{
    // document.getElementById('hiddenForm2').submit();
    var form = document.getElementById('hiddenForm2');
    // console.log('val:', form.elements['val'].value); // 값 확인용
    form.submit();
}

function delChk()
{
    alertc("삭제 확인", '정말로 삭제하시겠습니까?');

}
</script>

</section>
@endsection