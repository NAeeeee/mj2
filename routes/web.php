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

Route::get('/gg', function () {
    return view('welcome2');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/pwChange', [ProfileController::class, 'popup'])->name('profile.popup');
    Route::post('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/delete/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 사용자가 이메일 인증 링크 눌렀을 때 처리
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // 인증 완료 처리
        return redirect('/home'); // 인증 완료 후 이동할 곳
    })->middleware(['signed'])->name('verification.verify');

    // 인증 메일 재전송 버튼 누를 때 처리
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification(); // 메일 다시 보내기
        return back()->with('message', '인증 메일을 다시 보냈어!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/email/update-form', function () {
        return view('auth.email-update');
    });
    // 인증 전 이메일 수정
    Route::post('/email/update', [RegisteredUserController::class, 'updateEmail'])->name('email.update');
});

// 관리자
// Route::middleware(['auth'])->group(function () {
//     Route::get('/admin/list', [AdminController::class, 'list'])->name('admin.list');
// });

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/list', [AdminController::class, 'list'])->name('admin.list');
    Route::get('/users', [AdminController::class, 'userIndex'])->name('admin.users.index');
});

// 게시판
// Route::resource('boards', BoardController::class);
// Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
Route::middleware(['auth'])->prefix('boards')->group(function () {
    Route::get('/', [BoardController::class, 'index'])->name('boards.index');
    Route::post('/', [BoardController::class, 'store'])->name('boards.store');
    Route::get('/show/{board}', [BoardController::class, 'show'])->name('boards.show');
    // Route::get('/create', [BoardController::class, 'create'])->name('boards.create');
    Route::post('/delete/{id}', [BoardController::class, 'delete'])->name('boards.delete');
    // Route::post('/edit/{board}', [BoardController::class, 'edit'])->name('boards.edit');
    // 글 수정 폼 보여주기 (GET)
    Route::post('/{id}/edit', [BoardController::class, 'edit'])->name('boards.edit');
    // 글 수정 처리 (PUT or PATCH)
    Route::put('/edit/{id}', [BoardController::class, 'update'])->name('boards.update');
    Route::put('/{id}/status', [BoardController::class, 'updateStatus'])->name('boards.updateStatus');
    Route::post('/reply', [BoardController::class, 'reply'])->name('boards.reply');
    Route::get('/search', [BoardController::class, 'search'])->name('boards.search');
    // Route::post('/search', [BoardController::class, 'search'])->name('boards.search');
});

// 요청견적게시판
// Route::resource('request', RequestController::class);
Route::get('/request', [RequestController::class, 'index'])->name('request.index');
Route::post('/request', [RequestController::class, 'index'])->name('request.index');
Route::post('/request', [RequestController::class, 'store'])->name('request.store');
Route::get('/request/create', [RequestController::class, 'create'])->name('request.create');
Route::get('/request/list', [RequestController::class, 'list'])->name('request.list');
Route::post('/request/list', [RequestController::class, 'list'])->name('request.list');
Route::post('/request/delete/{id}', [RequestController::class, 'delete'])->name('request.delete');

Route::middleware(['auth'])->group(function () {
    Route::get('/message/inbox', [MessageController::class, 'inbox'])->name('message.inbox');
    Route::get('/message/sent', [MessageController::class, 'sent'])->name('message.sent');
    Route::get('/message/{id}', [MessageController::class, 'show'])->name('message.show');
    Route::post('/message', [MessageController::class, 'store'])->name('message.store');
});

Route::get('/phpinfo', function () {
    phpinfo();
});

require __DIR__.'/auth.php';
