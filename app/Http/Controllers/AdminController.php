<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Post;

class AdminController extends Controller
{
    // 관리자 페이지 접근 제어
    public function __construct()
    {
        $this->middleware('auth'); // 로그인한 사용자만
    }

    // 리스트 페이지
    public function list()
    {
        if (Auth::user()->is_admin) {
            $users = User::all();
            return view('admin.list', compact('users'));
        } else {
            abort(403, '접근 권한이 없습니다.');
        }
    }

    public function userIndex()
    {
        // 관리자 제외
        $users = User::where('is_admin', '!=', 'Y')
                ->orderby('no', 'asc')
                ->get();
        return view('admin.users.index', compact('users'));
    }
}
