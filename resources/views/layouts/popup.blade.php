@include('layouts.header')

<body>

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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">확인</button>
            </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>