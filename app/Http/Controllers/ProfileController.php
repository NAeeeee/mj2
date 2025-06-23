<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Carbon;
use Log;
use DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        Log::info(__METHOD__);

        $user = User::where('id',$request->id)
                // ->where('status','Y')
                ->first();
        // log::info($user);
        if( isset($user->created_at) )
        {
            $user->created_date = Carbon::parse($user->created_at)->format('Y-m-d');
        }

        // 작성한 견적요청 게시물
        $board = [];
        $board = DB::table('posts')
                ->where('user_id', $user->id)
                ->where('save_status', 'Y')
                ->orderby('no','asc')
                ->get();

        if( sizeof($board) > 0 )
        {
            $board->transform(function ($post) {
                $status = config('var.status');
                $board_div = config('var.board_div');

                $post->created_at = Carbon::parse($post->created_at)->format('Y-m-d');
                $post->updated_at = Carbon::parse($post->updated_at)->format('Y-m-d');
                $post->sta = $post->status;
                $post->status = $status[$post->status];
                $post->div = $board_div[$post->div];

                return $post;
            });
        }
        log::info($board);

        return view('profile.edit', compact('user', 'board'));

    }

    public function popup(Request $request)
    {
        Log::info(__METHOD__);
        
        if( isset($request->val) && $request->val == '' )
        {
            // 오류
        }

        $user = User::where('id',$request->val)->first();

        $div = $request->input('div');
        $view = ($div === 'ph') ? 'profile.ph' : 'profile.pw';

        return view($view, compact('user'));
    }


    public function update(Request $request, $id)
    {
        Log::info(__METHOD__);

        $request->validate([
            'pw' => 'required|min:7',
        ]);

        // 유저 찾기
        $user = User::find($id);

        $div = $request->div ?? '';
        $pw = $request->pw ?? '';
        $pw2 = $request->pw_confirmation ?? '';
        $ph = $request->ph ?? '';

        $result_msg = '';
        if( $div == '' )    // 비밀번호 변경
        {
            if( $pw != $pw2 )
            {
                // 비밀번호 다름 오류 처리
                $result_msg = '동일한 비밀번호를 입력해주세요.';
            }
            else if( Hash::check($request->pw, $user->password) )
            {
                $result_msg = '새 비밀번호가 기존 비밀번호와 같습니다.';
            }
            else
            {
                // 비밀번호 변경
                $user->password = Hash::make($pw);
                $user->save();

                $result_msg = '비밀번호가 성공적으로 변경되었습니다.';
            }
        }
        else
        {
            // 휴대폰 번호 변경
            if( !Hash::check($request->pw, $user->password) )
            {
                $result_msg = '비밀번호가 일치하지 않습니다.';
            }
            else
            {
                $user->update([
                    'ph' => $request->ph,
                ]);

                $result_msg = '휴대폰 번호가 성공적으로 변경되었습니다.';
            }
        } 
        log::info('msg = '.$result_msg);

        return redirect()->back()->with('pw_msg', $result_msg);
        // return response()->json(['message' => $result_msg]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request, $id)
    {
        Log::info(__METHOD__);
        log::info($id);

        $user = $request->user();
        if( $user->id != $id )
        {
            abort(403, '잘못된 접근입니다.');
        }

        $uu = User::find($user->id); // 혹은 Auth::id() 사용
        $uu->status = 'N';
        $uu->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', '탈퇴가 완료되었습니다.');
    }
}
