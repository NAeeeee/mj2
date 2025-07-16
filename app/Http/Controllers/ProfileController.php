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
use App\Models\Notice;
use Illuminate\Support\Carbon;
use Log;
use DB;
use App\Models\Post;
use App\Models\PostFile;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // 로그인한 사용자만
    }

    public function main(Request $request)
    {
        Log::info(__METHOD__);

        // 회원용 화면
        $notice = Notice::where('save_status','Y')
                    ->where('is_visible','Y')
                    ->orderby('no','desc')
                    ->limit(3)
                    ->get();
        
        $notice->transform(function ($nn) {
                $notice_div = config('var.notice_div');
                $nn->created_date = Carbon::parse($nn->created_at)->format('Y-m-d');

                $nn->div = $notice_div[$nn->div];

                return $nn;
            });

        // 활동중인 회원 수
        $state = [
                'user_cnt' => User::where('status', 'Y')->where('is_admin','N')->count(),
                'post_cnt' => Post::where('save_status', 'Y')
                                    ->whereIn('status',['A','B'])
                                    ->whereHas('user', function ($query) {
                                        $query->where('status', 'Y');
                                    })
                                    ->count(),
                'today_user_cnt'  => User::where('status', 'Y')
                                        ->whereDate('created_at', today())
                                        ->where('is_admin','N')
                                        ->count(),
                'today_post_cnt'  => Post::where('save_status', 'Y')
                                        ->whereDate('created_at', today())
                                        ->count(),
        ];

        $total = Post::where('save_status','Y')
                        ->whereHas('user', function ($query) {
                            $query->where('status', 'Y');
                        })->count();
        $sta_ab = Post::where('save_status','Y')
                        ->whereIn('status',['A','B'])
                        ->count();
                        

        $percentage = $total > 0 ? round(($state['post_cnt'] / $total) * 100, 1) : 0;


        return view('welcome' ,compact('state', 'percentage', 'notice'));
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        Log::info(__METHOD__);

        if( $request->id != auth()->user()->id )
        {
            abort(403);
        }

        $user = User::where('id',$request->id)
                // ->where('status','Y')
                ->first();

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
                if( $post->status === 'D' )
                {
                    if( $post->confirm_status === 'Y' )
                    {
                        $post->status = '고객 확인 완료';
                    }
                    else {
                        $post->status = '고객 확인중';
                    }
                }
                else
                {
                    $post->status = $status[$post->status];
                }
                $post->div = $board_div[$post->div];

                return $post;
            });
        }

        return view('profile.edit', compact('user', 'board'));

    }

    public function popup(Request $request)
    {
        Log::info(__METHOD__);
        
        if( isset($request->val) && $request->val == '' )
        {
            abort(404);
        }

        $user = User::where('id',$request->val)->first();

        $div = $request->input('div');

        $img = [];
        $img = (array) DB::table('post_file')
                ->where('target_no', $request->val)
                ->where('target_type','I')
                ->where('save_status', 'Y')
                ->first();

        if( !empty($img) )
        {
            $img['pathDate'] = explode('_', $img['savename'])[0];
        }

        return view('profile.info', compact('user', 'img'));
    }


    public function update(Request $request, $id)
    {
        Log::info(__METHOD__);


        // 유저 찾기
        $user = User::find($id);

        $pw = $request->pw ?? '';
        $pw2 = $request->pw_confirmation ?? '';
        $ph = $request->ph ?? '';
        
        $file = $request->file('file');

        if( isset($file) )
        {
            log::info('파일있음');
            // 이전에 있던 프로필사진 지우기
            $profile_img = (Array) DB::table('post_file')
                        ->where('target_no', $user->id)
                        ->where('target_type', 'I')
                        ->where('save_status', 'Y')
                        ->first();

            if( $profile_img )
            {
                log::info('[프로필] '.$profile_img['no'].' 삭제');
                Postfile::where('no', $profile_img['no'])->update([
                    'save_status' => 'N',
                ]);
            }

            $filename = $file->getClientOriginalName();
            log::info("파일명 : ".$filename);

            // 1. 경로: storage/app/public/img/날짜
            $path = 'public/img/' . now()->format('Ymd');

            // 2. 절대경로 구하기(storage/app/public/img/날짜)
            $fullPath = storage_path('app/public/img/' . now()->format('Ymd'));
            Log::info('저장 경로: ' . $fullPath);

            $size = $file->getSize();

            if (!\File::exists($fullPath)) 
            {
                \File::makeDirectory($fullPath, 0755, true);
            }

            $saveName = now()->format('Ymd_His') . '_' . $filename;
            Log::info("저장 파일명 : " . $saveName);

            $file->move($fullPath, $saveName);

            $url = asset(str_replace('public/', 'storage/', $path . '/' . $saveName));
            Log::info("접근 URL : " . $url);

            // DB에 저장
            $postFile = new PostFile();
            $postFile->target_no = $user->id;
            $postFile->filename = $filename;
            $postFile->filepath = $fullPath;
            $postFile->savename = $saveName;
            $postFile->filesize = $size;
            $postFile->filetype = $file->getClientMimeType();
            $postFile->extension = $file->getClientOriginalExtension();
            $postFile->save_status = 'Y';
            $postFile->target_type = 'I';   // 프로필
            $postFile->save();
        }


        $result_msg = '';
        if( $ph !== '' )
        {
            log::info('ph 변경');
            $ph = str_replace('-', '', $ph);
            if ( strlen($ph) === 11 ) 
            {
                $user->ph = $ph;
            } 
            else 
            {
                $result_msg = '휴대전화는 11자리로 입력해주세요.';

                return redirect()->back()->with('pw_msg', $result_msg);
            }
        }
        

        if( $pw !== '' )    // 비밀번호 변경
        {
            if( $pw != $pw2 )
            {
                // 비밀번호 다름 오류 처리
                $result_msg = '동일한 비밀번호를 입력해주세요.';

                return redirect()->back()->with('pw_msg', $result_msg);
            }
            else if( Hash::check($request->pw, $user->password) )
            {
                $result_msg = '새 비밀번호가 기존 비밀번호와 같습니다.';

                return redirect()->back()->with('pw_msg', $result_msg);
            }
            else
            {
                // 비밀번호 변경
                $user->password = Hash::make($pw);
            }
        }
        $result_msg = '정보 수정이 완료되었습니다.';
        log::info('msg = '.$result_msg);

        return redirect()->back()->with('pw_msg', $result_msg);
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
            abort(403);
        }

        $uu = User::find($user->id); // 혹은 Auth::id() 사용
        $uu->status = 'N';
        $uu->save();

        // $request->session()->flash('message', '탈퇴가 완료되었습니다.');

        Auth::logout();

        return redirect('/login')->with('msg_s', '탈퇴가 완료되었습니다.');
    }

    
    // 이메일 인증 전 이메일 변경
    public function emailEdit(Request $request)
    {
        Log::info(__METHOD__);
        
        $id = $request->id ?? '';

        if( $id == '' )
        {
            abort(404);
        }

        $info = User::find($id);

        return view('auth.email-update', compact('info'));
    }
}
