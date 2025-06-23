@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-3">
    <div class="container px-lg-5">
        <div class="mb-5">
            <h2 class="text-2xl font-bold mb-4">글 작성</h2>
        </div>

        <form id="boardForm" action="{{ route('boards.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">제목</label>
                <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700">내용</label>
                <textarea name="content" id="content" rows="6" class="w-full border rounded px-3 py-2" required></textarea>
            </div>

            <div class="mb-3">
                <label for="formFileMultiple" class="form-label">Multiple files input example</label>
                <input class="form-control" type="file" id="file" multiple>
            </div>

            <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded">
                작성하기
            </button>
        </form>
</div>

<script>

// 게시물 올리기전 체크
function createChk()
{
    var id = $("#title").val();
    var con = $("#content").val();
    var id = $("#title").val();

    // 항목, 제목, 내용 체크


    // document.getElementById('boardForm').submit();
}


</script>

</section>
@endsection