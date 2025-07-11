<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>403 - 접근 금지</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen px-4">
    <div class="text-center max-w-md">
        <img src="/img/ongi.jpg" class="mx-auto w-40 h-40 object-cover rounded-full shadow-lg mb-4">
        <h1 class="text-6xl font-extrabold text-gray-700 mb-2">403</h1>
        <p class="text-xl text-gray-800 mb-2">페이지를 확인해주세요.</p>
        <p class="text-sm text-gray-500 mb-6">권한이 없거나 접근이 차단된 페이지에요.</p>
        <a href="{{ route('main') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-5 rounded-full shadow">
            홈으로 돌아가기 🐾
        </a>
    </div>
</body>
</html>
