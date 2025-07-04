@include('layouts.header')

        <!-- Header-->
        <!-- <header class="py-5">
            <div class="container px-lg-5">
                <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
                    <div class="m-4 m-lg-5">
                        <h1 class="display-5 fw-bold">A warm welcome!</h1>
                        <p class="fs-4">Bootstrap utility classes are used to create this jumbotron since the old component has been removed from the framework. Why create custom CSS when you can use utilities?</p>
                        <a class="btn btn-primary btn-lg" href="#!">Call to action</a>
                    </div>
                </div>
            </div>
        </header> -->

<body>
    @include('layouts.nav')

        @yield('content')


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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal_btn">확인</button>
            </div>
            </div>
        </div>
    </div>

    <!-- 삭제 확인 팝업 -->
    <div class="modal fade" id="delModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST">
            @csrf
            <input type="hidden" name="delete_div" id="delete_div">
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

    <!-- Script -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- Core theme JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    {{-- 팝업(primary) --}}
    @if(session('msg'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                alertc('완료',"{{ session('msg') }}",'p');
            });
        </script>
    @endif

    {{-- 팝업(danger) --}}
    @if(session('msg_d'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                alertc("{{ session('title_d') }}","{{ session('msg_d') }}");
            });
        </script>
    @endif

    {{-- 팝업(secondary) --}}
    @if(session('msg_s'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                alertc('완료',"{{ session('msg_s') }}",'s');
            });
        </script>
    @endif

    
    {{-- 여기서 자식 뷰에서 푸시한 스크립트가 삽입됨 --}}
    @stack('scripts')
    <footer>
        @include('layouts.footer')
    </footer>
</body>
</html>