@include('layouts.header')

<body>
    @include('layouts.nav')
    <!-- <div class="container"> -->
        @yield('content')

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <script>
                // 3초(3000ms) 후 이전 페이지로 이동
                setTimeout(function() {
                    // window.location.href = '/';
                    location.reload();
                }, 3000);
            </script>
        @endif

        @if(session('msg'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('msg') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('message'))
            <script>
                alertc('완료',"{{ session('message') }}"); // 혹은 커스텀 모달로
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000); // 2초 뒤 홈으로 이동
            </script>
        @endif
    <!-- </div> -->

    <!-- 공용 팝업 -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div id="modal-header-s" class="modal-header text-white">
                <h5 class="modal-title" id="global-alert-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
            </div>
            <div class="modal-body" id="alertModalMsg">
                <!-- 메시지 들어감 -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">확인</button>
            </div>
            </div>
        </div>
    </div>

    <!-- 삭제 확인 팝업 -->
    <div class="modal fade" id="delModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">삭제 확인</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                    <div class="modal-body">
                    정말 삭제하시겠습니까?
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                    <button type="submit" class="btn btn-danger">삭제</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Script 추가 -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- 여기서 자식 뷰에서 푸시한 스크립트가 삽입됨 --}}
    @stack('scripts')

    <footer>
        @include('layouts.footer')
    </footer>
</body>
</html>