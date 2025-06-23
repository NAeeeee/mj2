<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Notification;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // 기본 라라벨 방식
        // $request->user()->sendEmailVerificationNotification();

         // 테스트용 메일
        Notification::route('mail', 'nazz0525z@gmail.com')
            ->notify(new CustomVerifyEmail($request->user()));

        return back()->with('status', 'verification-link-sent');
    }
}
