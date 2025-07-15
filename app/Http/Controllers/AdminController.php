<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Reply;
use App\Models\Notice;
use App\Models\Message;
use DB;
use Log;
use Carbon\Carbon;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // 관리자 페이지 접근 제어
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_admin'); // 관리자만 접근 가능
    }

    // 리스트 페이지
    public function list(Request $request)
    {
        Log::info(__METHOD__);

        $div = $request->div ?? '';
        $search_div = $request->search_div ?? '';
        $keyword = $request->search ?? '';

        $query = DB::table('users')
            ->orderby('id','asc');
        
        if ( $div === 'Y' ) 
        {
            $query->where('status', 'Y')
                    ->where('is_admin', 'N');
        }
        else if ( $div === 'N' ) 
        {
            $query->where('status', 'N');
        }
        else if ( $div === 'A' ) 
        {
            $query->where('is_admin', 'Y');
        }

        // 검색 조건 추가
        if ($search_div && $keyword) 
        {
            // 아이디
            if( $search_div === 'id' )
            {
                $query->where('id', 'like', "%{$keyword}%");
            }
            // 이름
            if( $search_div === 'name' )
            {
                $query->where('name', 'like', "%{$keyword}%");
            }
            // 이메일
            if( $search_div === 'email' )
            {
                $query->where('email', 'like', "%{$keyword}%");
            }
        }

        $users = $query->paginate(5)->withQueryString();

        $users->transform(function ($uu) {
            // 이름 마스킹
            if ( $uu->name ) 
            {
                if ( $uu->status === 'N' ) 
                {
                    $len = mb_strlen($uu->name);
                    if ( $len <= 1 ) 
                    {
                        $uu->name_r = '*';
                    } 
                    elseif ( $len === 2 ) 
                    {
                        $uu->name_r = mb_substr($uu->name, 0, 1) . '*';
                    } 
                    else 
                    {
                        $uu->name_r = mb_substr($uu->name, 0, 1) . str_repeat('*', $len - 2) . mb_substr($uu->name, -1);
                    }
                }
                else 
                {
                    $uu->name_r = $uu->name;
                }
            }

            // 이메일 마스킹
            if ( $uu->email ) 
            {
                if ( $uu->status === 'N' ) 
                {
                    $parts = explode('@', $uu->email);
                    $local = $parts[0];
                    $domain = $parts[1] ?? '';

                    $localLen = strlen($local);
                    if ( $localLen <= 3 ) 
                    {
                        $maskedLocal = str_repeat('*', $localLen);
                    } 
                    else 
                    {
                        $maskedLocal = substr($local, 0, 3) . str_repeat('*', $localLen - 3);
                    }

                    $uu->email_r = $maskedLocal . '@' . $domain;
                } 
                else 
                {
                    $uu->email_r = $uu->email;
                }
            }

            // 핸드폰 번호 마스킹
            if ( $uu->ph ) 
            {
                $ph = preg_replace('/[^0-9]/', '', $uu->ph);
                $ph_l = strlen($ph);

                if ( $ph_l === 11 ) 
                {
                    $uu->ph_r = ( $uu->status === 'N' )
                        ? substr($ph, 0, 3) . '-****-' . substr($ph, 7)
                        : substr($ph, 0, 3) . '-' . substr($ph, 3, 4) . '-' . substr($ph, 7);
                } 
                elseif ( $ph_l === 10 ) 
                {
                    $uu->ph_r = ( $uu->status === 'N' )
                        ? substr($ph, 0, 3) . '-***-' . substr($ph, 6)
                    : substr($ph, 0, 3) . '-' . substr($ph, 3, 3) . '-' . substr($ph, 6);
                } 
                else 
                {
                    $uu->ph_r = $ph; // 포맷 안맞는 경우 그대로 출력
                }
            }
                
            $uu->sta = $uu->status === 'Y' ? '활동' : '탈퇴';

            return $uu;
        });
            

        $sta = 
        [   
            'user_cnt' => User::where('status', 'Y')->where('is_admin', 'N')->count(),
            'admin_cnt' => User::where('is_admin', 'Y')->count(),
        ];

        return view('admin.list', compact('users', 'div', 'search_div', 'keyword', 'sta'));
    }


    public function add(Request $request)
    {
        Log::info(__METHOD__);
        
        $request->validate([
            'name' => ['required', 'string','lowercase', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'ph' => ['required','string'],
            'pw' => ['required', Rules\Password::defaults(), 'min:8'],
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->ph = $request->ph;
        $user->password = Hash::make($request->pw);
        $user->is_admin = 'Y';
        $user->email_verified_at = Carbon::now();
        $user->status = 'Y';
        $user->save();

        $result_msg = "계정 생성을 완료하였습니다.";

        return redirect()->back()->with('pw_msg', $result_msg);
    }


    public function info(Request $request)
    {
        Log::info(__METHOD__);
        
        $id = $request->id ?? '';

        if( $id === '' )
        {
            abort(404);
        }

        $user = User::where('id',$id)
                ->first();

        if( $user->status == 'N' )
        {
            $user->readonly = 'readonly';
        }

        if( $user->name )
        {
            if( $user->status == 'N' ) 
            {
                $len = mb_strlen($user->name);

                if ( $len <= 1 ) 
                {
                    $user->user_name = '*';
                } 
                elseif ( $len === 2 ) 
                {
                    $user->user_name = mb_substr($user->name, 0, 1) . '*';
                } 
                else 
                {
                    $user->user_name = mb_substr($user->name, 0, 1) . str_repeat('*', $len - 2) . mb_substr($user->name, -1);
                }
            } 
            else 
            {
                $user->user_name = $user->name;
            }
        }

        if( $user->ph )
        {
            $ph = preg_replace('/[^0-9]/', '', $user->ph);

            if ( strlen($ph) === 11 ) 
            {
                if ( $user->status === 'N' ) 
                {
                    $user->ph_r = substr($ph, 0, 3) . '-****-' . substr($ph, 7);
                } 
                else 
                {
                    $user->ph_r = substr($ph, 0, 3) . '-' . substr($ph, 3, 4) . '-' . substr($ph, 7);
                }
            } 
            elseif ( strlen($ph) === 10 ) 
            {
                if ( $user->status === 'N' ) 
                {
                    $user->ph_r = substr($ph, 0, 3) . '-***-' . substr($ph, 6);
                } 
                else 
                {
                    $user->ph_r = substr($ph, 0, 3) . '-' . substr($ph, 3, 3) . '-' . substr($ph, 6);
                }
            } 
            else 
            {
                $user->ph_r = $ph;
            }
        }

        if( $user->email )
        {
            if ( $user->status === 'N' ) 
            {
                $parts = explode('@', $user->email);
                $local = $parts[0];
                $domain = $parts[1] ?? '';

                $localLen = strlen($local);
                if ( $localLen <= 3 ) 
                {
                    $maskedLocal = str_repeat('*', $localLen);
                } 
                else 
                {
                    $maskedLocal = substr($local, 0, 3) . str_repeat('*', $localLen - 3);
                }

                $user->email_r = $maskedLocal . '@' . $domain;
            } 
            else 
            {
                $user->email_r = $user->email;
            }
        }

        return view('admin.info', compact('user'));
    }


    public function infoEdit(Request $request, $id)
    {
        Log::info(__METHOD__);

        if( $id === '' )
        {
            abort(404);
        }

        $user = User::find($id);

        $data = $request->only(['name', 'email', 'ph']);

        if ($request->filled('pw')) 
        {
            $data['password'] = bcrypt($request->pw);
        }

        $user->update($data);

        $result_msg = "정보 수정이 완료되었습니다.";

        return redirect()->back()->with('pw_msg', $result_msg);
    }


    public function infoDel(Request $request, $id)
    {
        Log::info(__METHOD__);

        log::info('[관리자 탈퇴] 요청자: '.auth()->user()->id);
        log::info('[관리자 탈퇴] 대상자: '.$id);


        DB::beginTransaction();
        try 
        {
            // 댓글 id 변경
            Reply::where('admin_id', $id)->update([
                'admin_id' => auth()->user()->id,
            ]);

            // 공지사항 작성자 변경
            Notice::where('save_id', $id)->update([
                'save_id' => auth()->user()->id,
            ]);

            // 메시지 id 변경
            Message::where('sender_id', $id)->update(['sender_id' => auth()->user()->id]);
            Message::where('receiver_id', $id)->update(['receiver_id' => auth()->user()->id]);

            // 관리자 탈퇴 처리
            User::where('id', $id)->update([
                'status' => 'N',
            ]);

            DB::commit();

            $msg = '수정이 완료되었습니다.';

            return redirect()->back()->with('pw_msg', $msg);

        } 
        catch (\Throwable $e) 
        {
            DB::rollBack();
            log::error('관리자 탈퇴 처리 중 오류: ' . $e->getMessage());
            throw $e;

            $msg = '관리자 탈퇴 처리 중 오류가 발생했습니다.';
            
            return redirect()->back()
                ->with('pw_msg', $msg);
        };

    }
}
