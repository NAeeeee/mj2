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

    // 게시판 리스트
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


    // 글쓰기
    // public function create()
    // {
    //     Log::info(__METHOD__);

    //     return view('boards.create');
    // }

    // 글저장
    public function store(Request $request)
    {
        Log::info(__METHOD__);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        \App\Models\Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id() ?? 1, // 로그인 안 됐을 경우 대비 (또는 익명)
        ]);


        return redirect()->route('boards.index')->with('success', '글이 작성되었습니다!');
    }

    public function show($no)
    {
        Log::info(__METHOD__);
        log::info(auth()->user());

        $post = \App\Models\Post::with('user')->findOrFail($no);
        
        // id가 관리자고 상태(status)가 'A'(요청접수)
        if (array_key_exists(auth()->user()->id, config('var.admin')) && $post->status == 'A') 
        {
            $post->status = "B"; // 담당자 확인중
            $post->save();
        }

        // 사진
        $img = [];
        $img = DB::table('post_file')
                ->where('target_no', $no)
                ->where('save_status', 'Y')
                ->orderby('no','asc')
                ->get();

        // 날짜 추출
        $img->transform(function ($i) {
            $i->pathDate = explode('_', $i->savename)[0]; // "20250604"

            return $i;
        });


        // 답글
        $reply = [];
        $reply = DB::table('post_replies')
                ->where('post_no', $no)
                ->where('save_status', 'Y')
                ->orderby('no','desc')
                ->first();

        // 답글이 있고 글 상태가 'C'(관리자 답변 완료) 일때 회원이 확인하면
        if( isset($reply->no) && $post->status='C' )
        {
            log::info($post->no.'번 게시물 상태 : '.$post->status);
            log::info("글쓴이 no : ".$post->user_id);
            if( auth()->user()->id == $post->user_id )
            {
                log::info('아이디 같음');
                log::info('진입 아이디 : '.auth()->user()->id);

                // 고객 확인 완료
                $post->status = "D";
                $post->save();

                // 관리자 댓글 상태도 고객확인완료 업데이트
                $reply_chk = \App\Models\Reply::findOrFail($reply->no);
                $reply_chk->status = "D";
                $reply_chk->save();
            }
            else
            {
                // 글쓴회원, 관리자만 진입가능
                // log::info('아이디 다름');
                // log::info('진입 아이디 : '.auth()->user()->id);
            }
;
        }

        $post->sta = config('var.status');
        log::info($post);

        return view('boards.show', compact('post', 'reply', 'img'));
    }

    // 수정 폼 보여주기
    public function edit($id)
    {
        Log::info(__METHOD__);

        $post = Post::findOrFail($id);

        // 사진
        $img = [];
        $img = DB::table('post_file')
                ->where('target_no', $id)
                ->where('save_status', 'Y')
                ->orderby('no','asc')
                ->get();

        // 날짜 추출
        $img->transform(function ($i) {
            $i->pathDate = explode('_', $i->savename)[0]; // "20250604"

            return $i;
        });

        return view('boards.edit', compact('post' ,'img'));
    }

    public function update(Request $request, $id)
    {
        Log::info(__METHOD__);

        $post = Post::findOrFail($id);


        $request->validate([
            'div' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            // 추가 필드 있으면 넣기
        ]);

        // dd($request);

        // 업로드한 파일 삭제
        if( isset($request->delete_files) )
        {
            // 배열 형태로 옴
            $del = $request->delete_files;

            foreach($del as $d)
            {
                $ff = PostFile::findOrFail($d);

                log::info("File ".$d."번 삭제");
                $ff->save_status = "N";
                $ff->save();
            }
        }

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
                $postFile->target_no = $post->no; // posts 기본키가 no임에 유의
                $postFile->filename = $filename;
                $postFile->filepath = $fullPath;
                $postFile->savename = $saveName;
                $postFile->filesize = $size;
                $postFile->filetype = $file->getClientMimeType();
                $postFile->extension = $file->getClientOriginalExtension();
                $postFile->save_status = 'Y';
                $postFile->target_type = 'P';
                $postFile->save();

                Log::info("파일 저장 db 성공");

            }
        }
        
        Post::where('no', $id)->update([
            'div' => $request->input('div'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        // 수정 내용 저장
        // $post->div = $request->input('div');
        // $post->title = $request->input('title');
        // $post->content = $request->input('content');
        // 필요하면 다른 필드도 저장
        // $post->save();

        return redirect()->route('boards.show', $post->no)
                         ->with('success', '게시글이 수정되었습니다.');
    }

    // 삭제
    public function delete($no)
    {
        Log::info(__METHOD__);

        $board = Post::findOrFail($no);
        // Log::info($board);

        // 데이터 수정
        $board->save_status = "N";
        $board->save();
        
        return redirect()->back()->with('success', '삭제 완료');
    }

    // 관리자가 견적요청상태 변경
    public function updateStatus(Request $request, $no)
    {
        Log::info(__METHOD__);

        $request->validate([
            'status' => 'required|in:A,B,C', // 허용된 값만 통과
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
            return redirect()->back()->with('msg', $msg);
        }
        else
        {
            $post = Post::findOrFail($no);
            $post->status = $request->status;
            $post->save();

            return redirect()->back()->with('success', '상태가 변경되었습니다.');
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
