@extends('layouts.app2')

@section('content')
<section class="pt-3 mt-4">
    <div class="container px-lg-5">

    <div class="mb-5 with3">
        <h2 class="text-2xl font-bold">공지 수정</h2>

        <a href="{{ route('notice.list') }}" class="btn btn-dark">공지 목록</a>
    </div>
    
    <form id="Form" action="{{ route('notice.update', $notice->no) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3 with_gap">
            <select name="div" id="div" class="form-select w-auto d-inline-block me-2" value="{{ old('div', $notice->div) }}" required>
                <option value="">항목</option>
                <option value="A" {{ old('div', $notice->div ?? '') == 'A' ? 'selected' : '' }}>공지</option>
                <option value="B" {{ old('div', $notice->div ?? '') == 'B' ? 'selected' : '' }}>이벤트</option>
                <option value="C" {{ old('div', $notice->div ?? '') == 'C' ? 'selected' : '' }}>안내</option>
            </select>

            <input type="text" name="title" id="title" class="form-control title_d" 
                   value="{{ old('title', $notice->title) }}" required>
            
            <div>
                <select name="save_status" id="save_status" class="form-select w-auto d-inline-block me-2" value="{{ old('save_status', $notice->save_status) }}" required>
                    <option value="Y" {{ old('save_status', $notice->save_status ?? '') == 'Y' ? 'selected' : '' }}>저장</option>
                    <option value="N" {{ old('save_status', $notice->save_status ?? '') == 'N' ? 'selected' : '' }}>삭제</option>
                </select>

                <select name="is_visible" id="is_visible" class="form-select w-auto d-inline-block" value="{{ old('is_visible', $notice->is_visible) }}" required>
                    <option value="Y" {{ old('is_visible', $notice->is_visible ?? '') == 'Y' ? 'selected' : '' }}>활성화</option>
                    <option value="N" {{ old('is_visible', $notice->is_visible ?? '') == 'N' ? 'selected' : '' }}>비활성화</option>
                </select>
            </div>
        </div>

        <!-- <div class="with mb-1"> -->
        <div class="mb-4">
            <textarea name="content" id="content" rows="6" 
                    class="form-control w-full border rounded px-3 py-2" required>{{ old('content', $notice->content) }}</textarea>
        </div>
        @php
            $maxImages = 3;
            $cnt = count($img);
            $mct = $maxImages - $cnt;
        @endphp

        <div class="with mb-3">
        {{-- 기존 업로드된 이미지 출력 --}}
        @foreach ($img as $image)
            <div class="d-inline-block position-relative" style="width: 300px;">
                <img src="{{ asset('storage/img/' . $image->pathDate . '/' . $image->savename) }}" 
                    class="img-thumbnail w-100">

                <input type="checkbox" name="delete_files[]" value="{{ $image->no }}" 
                    id="delete_file_{{ $image->no }}" class="d-none">
                
                <label for="delete_file_{{ $image->no }}" 
                    class="position-absolute top-0 end-0 m-1 btn btn-sm btn-danger rounded-circle"
                    style="cursor: pointer; user-select:none; display:flex; align-items:center; justify-content:center; width:24px; height:24px;">
                    <i class="bi bi-x-circle"></i>
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
            <button type="button" class="btn btn-primary" onclick="upload('notice')">
                수정
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
