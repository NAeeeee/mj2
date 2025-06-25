<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->middleware(['auth', 'verified']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/pwChange', [ProfileController::class, 'popup'])->name('profile.popup');
    Route::get('/phChange', [ProfileController::class, 'popup'])->name('profile.pwPopup');
    Route::post('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/delete/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 사용자 이메일 인증
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // 인증 완료 처리
        return redirect('/home'); // 인증 완료 후 이동할 곳
    })->middleware(['signed'])->name('verification.verify');

    // 인증 메일 재전송
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification(); // 메일 다시 보내기
        return back()->with('www', '재전송 완료!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/email/update-form', function () {
        return view('auth.email-update');
    });

    // 인증 전 이메일 수정
    Route::post('/email/update', [RegisteredUserController::class, 'updateEmail'])->name('email.update');
});


// 관리자 기능
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/list', [AdminController::class, 'list'])->name('admin.list');
    Route::get('/add', function () {
        return view('admin.admin-add');
    });
    Route::post('/add', [AdminController::class, 'add'])->name('admin.add');
    Route::get('/info', [AdminController::class, 'info'])->name('admin.info');
    Route::post('/info/{id}', [AdminController::class, 'infoEdit'])->name('admin.infoEdit');
});

// 게시판
Route::middleware(['auth'])->prefix('boards')->group(function () {
    // 관리자 메인 진입
    Route::get('/', [BoardController::class, 'index'])->name('boards.index');
    // 게시물 상태 변환(ex, 요청->검토)
    Route::put('/{id}/status', [BoardController::class, 'updateStatus'])->name('boards.updateStatus');
    // 게시물 답변 달기
    Route::post('/reply', [BoardController::class, 'reply'])->name('boards.reply');
    // 게시물 검색
    Route::get('/search', [BoardController::class, 'search'])->name('boards.search');
});

// 게시판 (회원)
Route::middleware(['auth'])->prefix('request')->group(function () {
    // 메인 진입
    Route::get('/', [RequestController::class, 'index'])->name('request.index');
    // 글쓰기 화면
    Route::get('/create', [RequestController::class, 'create'])->name('request.create');
    // 글 저장 처리
    Route::post('/', [RequestController::class, 'store'])->name('request.store');
    // 리스트
    Route::get('/list', [RequestController::class, 'list'])->name('request.list');
    // 게시글 상세 보기
    Route::get('/show/{id}', [RequestController::class, 'show'])->name('request.show');
    // 수정 화면
    Route::get('/edit/{id}', [RequestController::class, 'edit'])->name('request.edit');
    // 수정 처리
    Route::put('/edit/{id}', [RequestController::class, 'update'])->name('request.update');
    // 삭제
    Route::post('/delete/{id}', [RequestController::class, 'delete'])->name('request.delete');
});


// 쪽지
Route::middleware(['auth'])->prefix('message')->group(function () {
    Route::get('/inbox', [MessageController::class, 'inbox'])->name('message.inbox');
    Route::get('/{id}', [MessageController::class, 'show'])->name('message.show');
    Route::post('/', [MessageController::class, 'store'])->name('message.store');
});

// php 정보
Route::get('/phpinfo', function () {
    phpinfo();
});

require __DIR__.'/auth.php';
