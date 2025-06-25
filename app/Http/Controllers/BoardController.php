<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\Reply;
use App\Models\User;
use DB;
use Carbon\Carbon;

class BoardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // 로그인한 사용자만
    }

    // 게시판 리스트(관리자용)
    public function index(Request $request)
    {
        Log::info(__METHOD__);
        log::info($request);

        // div : 탭, search_div : 항목, keyword : 검색어
        $div = $request->div ?? '';
        $search_div = $request->search_div ?? '';
        $keyword = $request->search ?? '';


        $query = DB::table('posts')
                ->leftJoin('post_replies', function ($join) {
                    $join->on('posts.no', '=', 'post_replies.post_no')
                        ->where('post_replies.save_status', '=', 'Y');
                })
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->select(
                    'posts.no', 'posts.title', 'posts.user_id', 'posts.created_at', 'posts.status', 'posts.save_status', 'posts.div',
                    'users.name',
                    'users.status as users_status',
                    'post_replies.created_at as reply_at'
                )
                ->orderBy('posts.no', 'asc');

            if ( $div === 'X' ) 
            {
                // 미답변만
                $query->whereNull('post_replies.no')
                        ->where('users.status', 'Y')
                        ->where('posts.save_status', 'Y');
            } 
            elseif ( $div === 'O' ) 
            {
                // 답변 완료만
                $query->whereNotNull('post_replies.no');
            } 
            elseif ( in_array($div, ['A', 'B', 'C', 'D']) ) 
            {
                // 게시물 분류 필터
                $query->where('posts.div', $div);
            }
            elseif ( $div === 'E' ) 
            {
                // 반려
                $query->where('posts.status', 'E');
            }
            elseif ( $div === 'Z' ) 
            {
                // 처리완료
                $query->where('posts.status', 'Z');
            }
            elseif ( $div === 'del' ) 
            {
                // 삭제된 게시물
                $query->where('posts.save_status', 'N');
            }
            elseif ( $div === 'delu' )
            {
                // 탈퇴한 회원들 게시물
                $query->where('users.status', 'N');
            }

             // 검색 조건 추가
            if ($search_div && $keyword) 
            {
                // 작성자
                if ($search_div === 'user_id') 
                {
                    $query->where('users.name', 'like', "%{$keyword}%");
                }
                // 제목
                elseif ($search_div === 'title') 
                {
                    $query->where('posts.title', 'like', "%{$keyword}%");
                }
                // 내용
                elseif ($search_div === 'content') 
                {
                    $query->where('posts.content', 'like', "%{$keyword}%");
                }
            }

        $posts = $query->paginate(5)->withQueryString();

        $posts->transform(function ($post) {
            $rdiv = config('var.board_div');

            $post->div = $rdiv[$post->div] ?? $post->div;

            return $post;
        });

        // $view = ($div == 'X') ? 'boards.index' : 'boards.index2';
        $view = 'boards.index';

        return view($view, compact('posts', 'div', 'search_div', 'keyword'));
    }


    // 관리자가 견적요청상태 변경
    public function updateStatus(Request $request, $no)
    {
        Log::info(__METHOD__);

        $request->validate([
            'status' => 'required|in:E,Z', // 허용된 값만 통과
        ]);

        // 답글 달렸는지 확인
        $chkReply = [];
        $chkReply = DB::table('post_replies')
                    ->where('save_status','Y')
                    ->where('post_no',$no)
                    ->first();
        
        // 답글 없으면 상태변경 불가능
        if( !$chkReply )
        {
            $msg = '관리자 답글이 없는 게시물입니다. ('.$no.'번)';
            return redirect()->back()
                ->with('title_d', '확인 요청')
                ->with('msg_d', $msg);
        }
        else
        {
            $post = Post::findOrFail($no);
            $post->status = $request->status;
            $post->save();

            return redirect()->back()
                            ->with('title_d', '완료')
                            ->with('msg_p2', '상태가 변경되었습니다.');
        }

    }

    // 관리자 견적요청 답글
    public function reply(Request $request)
    {
        log::info(__METHOD__);
        // dd($request);
        
        $request->validate([
            'admin_id' => 'required',
            'post_no' => 'required',
            'content' => 'required|string',
        ]);

        // 파일 저장
        $files = $request->file('file');

        if( isset($files) )
        {
            foreach ($files as $file) 
            {
                $filename = $file->getClientOriginalName();
                log::info("파일명 : ".$filename);

                // 경로 없으면 생성 (img/날짜)
                $path = 'img/' . now()->format('Ymd');
                $fullPath = public_path($path);
                $size = $file->getSize();
                Log::info("사이즈 : " . $size);

                if (!\File::exists($fullPath)) {
                    \File::makeDirectory($fullPath, 0755, true);
                }

                $saveName = now()->format('Ymd_His').'_'.$filename;
                Log::info("경로 : " . $fullPath);
                Log::info("저장 파일명 : ".$saveName);
                $file->move($fullPath, $saveName); // ← 여기가 핵심

                // DB에 저장
                $postFile = new PostFile();
                $postFile->target_no = $request->post_no; // posts 기본키가 no임에 유의
                $postFile->filename = $filename;
                $postFile->filepath = $fullPath;
                $postFile->savename = $saveName;
                $postFile->filesize = $size;
                $postFile->filetype = $file->getClientMimeType();
                $postFile->extension = $file->getClientOriginalExtension();
                $postFile->save_status = 'Y';
                $postFile->target_type = 'R';
                $postFile->save();

                Log::info("[관리자] 파일 저장 db 성공");

            }
        }

        \App\Models\Reply::create([
            'post_no' => $request->post_no,
            'content' => $request->content,
            'admin_id' => $request->admin_id,
            'status' => 'C',                // 검토
            'save_status' => 'Y',
        ]);

        // 그러면서 견적요청 게시판 상태 'C'(답변)로 업데이트
        $post = Post::findOrFail($request->post_no);
        $post->status = 'C';
        $post->save();

        // 보낸 쪽지 (관리자 보낸편지함용)
        \App\Models\Message::create([
            'title' => '게시물 답변 완료 [ '.date('Y-m-d').' ]',
            'sender_id' => $request->admin_id, // DB는 일단 관리자ID
            'receiver_id' => $request->user_id,
            'div' => 'S',                // 알림
            'content' => $request->content,
            'is_read' => 0,
            'post_no' => $request->post_no,
            'save_status' => 'Y',
        ]);

        // 받은 쪽지 (회원 받은편지함용)
        \App\Models\Message::create([
            'title' => '게시물 답변 완료 [ '.date('Y-m-d').' ]',
            'sender_id' => $request->admin_id,   // 보낸 사람은 관리자 그대로
            'receiver_id' => $request->user_id,  // 받는 사람은 회원 그대로
            'div' => 'R',                         // 받은편지
            'content' => $request->content,
            'is_read' => 0,
            'post_no' => $request->post_no,
            'save_status' => 'Y',
        ]);

        return redirect()->route('boards.index')->with('success', '답글 작성되었습니다!');
    }

}
