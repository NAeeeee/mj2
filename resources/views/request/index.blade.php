@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">
    
    <form id="hiddenForm" method="POST" action="{{ route('request.list') }}">
        @csrf
        <input type="hidden" name="id" value="{{ $request->id }}">
    </form>

    <div class="mb-4 with3">
        <h2 class="text-2xl font-bold">글작성</h2>
        <button type="button" class="btn btn-dark" onclick="submitForm()">견적 확인</button>
    </div>
    
    <form id="Form" method="POST" action="{{ route('request.store') }}" class="mt-4" enctype="multipart/form-data">
        @csrf
        <div class="mb-3 with_gap">
            <select name="div" id="div" class="form-select w-auto d-inline-block me-2" value="">
                <option value="">항목</option>
                <option value="A">견적</option>
                <option value="B">배송</option>
                <option value="C">회원계정</option>
                <option value="D">기타</option>
            </select>

            <input type="input" class="form-control title_d" id="title" name="title">
        </div>

        <div class="with mb-1">
            <div>
                <label for="content" class="form-label">content</label>
                <textarea class="form-control textarea_content" id="content" name="content" rows="5"></textarea>
            </div>

            <div class="file-zone">
                <div class="mb-3">
                    <div class="form-text2 form-label" id="basic-addon5">✔ 첨부 가능한 확장자 : jpg, png</div></label>
                    <input class="form-control" type="file" id="file" name="file[]">
                </div>
                <div class="mb-3">
                    <input class="form-control" type="file" id="file2" name="file[]">
                </div>
                <div class="mb-3">
                    <input class="form-control" type="file" id="file3" name="file[]">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end" style="margin-bottom:1.5rem">
            <button type="button" class="btn btn-primary" onclick="upload('create')">
                작성하기
            </button>
        </div>
    </form>
    </div>

<script>
function submitForm() 
{
    document.getElementById('hiddenForm').submit();
}

function upload()
{

    var d = document.getElementById('div').value;
    var t = document.getElementById('title').value;
    var c = document.getElementById('content').value;

    // 검사
    if (d === '') {
        alertc('확인 요청','항목을 선택해주세요.');
        return false;
    }
    else if (t === '') {
        alertc('확인 요청','제목을 입력해주세요.');
        return false;
    }
    else if ( c === '' ) {
        alertc('확인 요청','내용을 입력해주세요.');
        return false;
    }

    // 파일 용량/타입 체크
    var atype = ['image/jpeg', 'image/png'];
    var max = 3 * 1024 * 1024; // 2MB

    if (!checkFiles()) {
        return false; // 파일 체크 실패시 제출 안 함
    }

    console.log('조건모두통과');
    document.getElementById('Form').submit();
}
</script>

</section>
@endsection




