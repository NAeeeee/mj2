<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>404 - 페이지 없음</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen px-4">
    <div class="text-center max-w-md">
        <img src="/img/ongi.jpg" class="mx-auto w-40 h-40 object-cover rounded-full shadow-lg mb-4" alt="옹이">
        <h1 class="text-6xl font-extrabold text-gray-700 mb-2">404</h1>
        <p class="text-xl text-gray-800 mb-2">페이지를 확인해주세요.</p>
        <p class="text-sm text-gray-500 mb-6">주소가 잘못되었거나, 페이지가 사라졌을 수 있어요.</p>
        <a href="{{ route('main') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-5 rounded-full shadow">
            홈으로 가기 🐾
        </a>
    </div>
</body>
</html>
