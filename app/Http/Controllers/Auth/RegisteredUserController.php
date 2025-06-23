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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'ph' => $request->ph,
            'password' => Hash::make($request->password),
            'status' => 'Y',
        ]);

        // 임시 이메일 고정
        $user->email = 'nazz0525z@gmail.com';

        event(new Registered($user));

        Auth::login($user);

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

        // 임시 이메일 고정
        $user->email = 'nazz0525z@gmail.com';

        // 인증 메일 다시 발송
        event(new Registered($user));

        return back()->with('message', '이메일이 변경되었고 인증 메일이 다시 전송되었습니다.');
    }
}
