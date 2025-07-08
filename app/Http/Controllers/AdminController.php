<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
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
        $this->middleware('auth'); // 로그인한 사용자만
    }

    // 리스트 페이지
    public function list(Request $request)
    {
        Log::info(__METHOD__);

        $div = $request->div ?? '';
        $search_div = $request->search_div ?? '';
        $keyword = $request->search ?? '';

        if ( Auth::user()->is_admin === 'Y' ) 
        {
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
            

            $user_cnt = User::where('status', 'Y')->where('is_admin', 'N')->count();

            return view('admin.list', compact('users', 'div', 'search_div', 'keyword', 'user_cnt'));
        }
        else 
        {
            abort(403);
        }
    }


    public function add(Request $request)
    {
        Log::info(__METHOD__);
        // log::info($request);
        
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

        $result_msg = "정보가 수정되었습니다.";

        return redirect()->back()->with('pw_msg', $result_msg);
    }
}
