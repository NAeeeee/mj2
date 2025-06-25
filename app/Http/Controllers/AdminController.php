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

        if ( Auth::user()->is_admin == 'Y' ) 
        {
            $query = DB::table('users')
                ->orderby('id','asc');
            
            if ( $div === 'O' ) 
            {
                $query->where('status', 'Y');
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
                $uu->status = $uu->status === 'Y' ? '활동' : '탈퇴';

                return $uu;
            });

            return view('admin.list', compact('users', 'div', 'search_div', 'keyword'));
        } else 
        {
            abort(403, '접근 권한이 없습니다.');
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
            abort(403, '잘못된 접근입니다.');
        }

        $user = User::where('id',$id)
                ->first();

        return view('admin.info', compact('user'));
    }

    public function infoEdit(Request $request, $id)
    {
        Log::info(__METHOD__);

        if( $id === '' )
        {
            abort(403, '잘못된 접근입니다.');
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
