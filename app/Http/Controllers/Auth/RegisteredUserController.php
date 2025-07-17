<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Log;
use App\Mail\VerifyEmailWithSES;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CustomVerifyEmail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        log::info($request);

        $request->validate([
            'name' => ['required', 'string','lowercase', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'ph' => ['required','string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->ph = str_replace('-', '', $request->ph);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'ph' => $request->ph,
            'password' => Hash::make($request->password),
            'status' => 'Y',
        ]);

        Auth::login($user);

        if (app()->environment('local')) {

            $user->notify(new CustomVerifyEmail());
        }
        else
        {
            // 운영계: SES 커스텀 메일 전송
            Mail::to($user->email)->send(new VerifyEmailWithSES($user));
        }

        return redirect(RouteServiceProvider::HOME);
    }


    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $user = auth()->user();

        // 이메일 변경 및 인증 초기화
        $user->update([
            'email' => $request->email,
            'email_verified_at' => null,
        ]);

        if (app()->environment('local')) {

            $user->notify(new CustomVerifyEmail());
        } 
        else
        {
            // 운영계: SES 커스텀 메일 전송
            Mail::to($user->email)->send(new VerifyEmailWithSES($user));
        }

        return back()->with('message', '이메일이 변경되었고 인증 메일이 다시 전송되었습니다.');
    }
}
