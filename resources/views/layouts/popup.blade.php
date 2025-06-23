@include('layouts.header')

<body>

    <!-- <div class="container"> -->
        @yield('content')
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
</body>
</html>