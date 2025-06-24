@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">

    <div class="mb-5">
        <h2 class="text-2xl font-bold">글수정</h2>
    </div>
    
    <form id="Form" action="{{ route('request.update', $post->no) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3 with_gap">
            <select name="div" id="div" class="form-select w-auto d-inline-block me-2" value="{{ old('div', $post->div) }}" required>
                <option value="">항목</option>
                <option value="A" {{ old('div', $post->div ?? '') == 'A' ? 'selected' : '' }}>견적</option>
                <option value="B" {{ old('div', $post->div ?? '') == 'B' ? 'selected' : '' }}>배송</option>
                <option value="C" {{ old('div', $post->div ?? '') == 'C' ? 'selected' : '' }}>회원계정</option>
                <option value="D" {{ old('div', $post->div ?? '') == 'D' ? 'selected' : '' }}>기타</option>
            </select>

            <input type="text" name="title" id="title" class="form-control title_d" 
                   value="{{ old('title', $post->title) }}" required>
        </div>

        <!-- <div class="with mb-1"> -->
        <div class="mb-4">
            <label for="content" class="form-label">내용</label>
            <textarea name="content" id="content" rows="6" 
                    class="form-control w-full border rounded px-3 py-2" required>{{ old('content', $post->content) }}</textarea>
        </div>
        @php
            $maxImages = 3;
            $cnt = count($img);
            $mct = $maxImages - $cnt;
        @endphp

        <div class="with mb-3">
        {{-- 기존 업로드된 이미지 출력 --}}
        {{-- @foreach ($img as $image)
            <div class="file-upload-box mb-4">
                <div class="mb-2">
                    <img src="{{ asset('img/' . $image->pathDate . '/' . $image->savename) }}" width="300">
                    <label>
                        <input type="checkbox" name="delete_files[]" value="{{ $image->no }}">
                        삭제
                    </label>
                </div>
            </div>
        @endforeach --}}

        @foreach ($img as $image)
            <div class="d-inline-block position-relative" style="width: 300px;">
                <img src="{{ asset('img/' . $image->pathDate . '/' . $image->savename) }}" 
                    class="img-thumbnail w-100">

                <input type="checkbox" name="delete_files[]" value="{{ $image->no }}" 
                    id="delete_file_{{ $image->no }}" class="d-none">
                
                <label for="delete_file_{{ $image->no }}" 
                    class="position-absolute top-0 end-0 m-1 btn btn-sm btn-danger rounded-circle"
                    style="cursor: pointer; user-select:none; display:flex; align-items:center; justify-content:center; width:24px; height:24px;">
                    <i class="bi bi-x-circle"></i>  <!-- fontawesome x 아이콘 예시 -->
                </label>
            </div>
        @endforeach

        {{-- 남은 슬롯만큼 첨부 필드 표시 --}}
        @for ($i = 0; $i < $mct; $i++)
            <div class="file-upload-box mb-4">
                <input class="form-control" type="file" name="file[]">
            </div>
        @endfor
        </div>

        <div class="d-flex justify-content-end" style="margin-bottom:1.5rem">
            <button type="button" class="btn btn-primary" onclick="upload()">
                수정하기
            </button>
        </div>
    </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('input[name="delete_files[]"]').on('change', function () {
        const imgBox = $(this).closest('.position-relative');
        const img = imgBox.find('img');

        if ($(this).is(':checked')) {
            img.css({
                'opacity': '0.4',
                'filter': 'grayscale(100%)'
            });
        } else {
            img.css({
                'opacity': '1',
                'filter': 'none'
            });
        }
    });
});
</script>

</section>
@endsection
