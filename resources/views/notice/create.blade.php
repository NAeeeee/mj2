@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">

    <div class="mb-4 with3">
        <h2 class="text-2xl font-bold">공지 작성</h2>
    </div>
    
    <form id="Form" method="POST" action="{{ route('notice.store') }}" class="mt-4" enctype="multipart/form-data">
        @csrf
        <div class="mb-3 with_gap">
            <select name="div" id="div" class="form-select w-auto d-inline-block me-2">
                <option value="">항목</option>
                <option value="A">공지</option>
                <option value="B">이벤트</option>
                <option value="C">안내</option>
            </select>

            <input type="input" class="form-control title_d" id="title" name="title">
        </div>

        <div class="with mb-1">
            <div>
                <label for="content" class="form-label">content</label>
                <textarea class="form-control textarea_content" id="content" name="content" rows="7" style="width: 640px;"></textarea>
            </div>

            <div class="file-zone">
                <div class="mb-2">
                    <div class="form-text2 form-label" id="basic-addon5">✔ 첨부 가능한 확장자 : jpg, png</div></label>
                    <div class="form-text2 form-label" id="basic-addon5">✔ 파일 1개당 3MB 까지 첨부 가능</div></label>
                    <input class="form-control" type="file" id="file" name="file[]">
                </div>
                <div class="mb-2">
                    <input class="form-control" type="file" id="file2" name="file[]">
                </div>
                <div class="mb-2">
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



</script>

</section>
@endsection




