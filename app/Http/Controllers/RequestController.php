<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\User;
use DB;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // 로그인한 사용자만
    }


    // 게시판 리스트
    public function index(Request $request)
    {
        Log::info(__METHOD__);


        $posts = DB::table('posts')
                ->where('user_id', $request->id)
                ->where('save_status', 'Y')
                ->orderby('no','asc')
                ->paginate(5)
                ->withQueryString();
        
        // 진입 시 작성글이 있으면 리스트를 보여주고 없다면 글작성 폼
        if ($posts->isEmpty())
        {
            return view('request.index')->with('request', $request);
        } 
        else
        {
            $posts->transform(function ($post) {
                $status = config('var.status');
                $board_div = config('var.board_div');

                $post->created_at = Carbon::parse($post->created_at)->format('Y-m-d');
                $post->updated_at = Carbon::parse($post->updated_at)->format('Y-m-d');
                $post->sta = $post->status;
                $post->status = $status[$post->status];
                $post->div = $board_div[$post->div];

                return $post;
            });

            return view('request.list')->with('posts', $posts);
        }

    }


    // 게시물 작성 폼 보여주기
    public function create(Request $request)
    {
        log::info($request);

        return view('request.index')->with('request', $request);
    }


    // 게시물 저장
    public function store(Request $request)
    {
        Log::info(__METHOD__);
        log::info($request);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'div' => 'required|string',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // 최대 2MB
        ]);

        $post = \App\Models\Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'div' => $request->div,
            'user_id' => auth()->id(),
            'status' => 'A', // 상태
            'save_status' => 'Y',
        ]);

        $createdNo = $post->no; // 생성된 게시물 no

        if ($request->hasFile('file')) {
            log::info("RequestController 파일있음");
            foreach ($request->file('file') as $file) {

                if ($file && $file->isValid()) {
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
                    $postFile->target_no = $createdNo; // posts 기본키가 no임에 유의
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
        }
        else
        {
            log::info("파일 업승ㅁ...");
            log::info($request);
        }

        return redirect()->route('request.show', ['id' => $post->no])
                ->with('msg', '글이 작성되었습니다!');
    }


    // 게시물 리스트
    public function list(Request $request)
    {
        Log::info(__METHOD__);

        $posts = [];
        $posts = DB::table('posts')
                ->where('user_id', $request->id)
                ->where('save_status', 'Y')
                ->orderby('no','asc')
                ->paginate(5)
                ->withQueryString();

        $posts->transform(function ($post) {
            $status = config('var.status');
            $board_div = config('var.board_div');

            $post->created_at = Carbon::parse($post->created_at)->format('Y-m-d');
            $post->updated_at = Carbon::parse($post->updated_at)->format('Y-m-d');
            $post->sta = $post->status;
            $post->status = $status[$post->status];
            $post->div = $board_div[$post->div];

            return $post;
        });

        return view('request.list')->with('posts', $posts);
    }


    // 게시물 상세보기
    public function show($no)
    {
        Log::info(__METHOD__);
        // log::info(auth()->user());

        $post = \App\Models\Post::with('user')->findOrFail($no);
        
        log::info($post['user']->status);
        // 관리자외 삭제된 글 접근x
        if( !array_key_exists(auth()->user()->id, config('var.admin')) && $post->save_status == 'N' )
        {
            return redirect('/')
                ->with('title_d', '확인 요청')
                ->with('msg_d', '삭제된 글입니다.');
        }

        // id가 관리자고 상태(status)가 'A'(요청접수)
        if ( array_key_exists(auth()->user()->id, config('var.admin')) && $post->status == 'A' ) 
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

        }

        $post->sta = config('var.status');

        return view('request.show', compact('post', 'reply', 'img'));
    }


    // 게시물 수정 폼 보여주기
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

        return view('request.edit', compact('post' ,'img'));
    }


    // 게시물 수정 처리
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

        return redirect()->route('request.show', $post->no)
                         ->with('success', '게시글이 수정되었습니다.');
    }


    // 게시물 삭제
    public function delete(Request $request, $no)
    {
        Log::info(__METHOD__);

        $div = $request->input('delete_div') ?? '';

        $board = Post::findOrFail($no);

        // 데이터 수정
        $board->save_status = 'N';
        $board->save();

        if( $div == 'request' )
        {
            // 삭제 완료 후 리스트화면
            return redirect()->route('request.list', ['id' => $board->user_id])
                ->with('msg', '글이 성공적으로 삭제되었습니다.');
        }
        else
        {
            // 리다이렉트 및 플래시 메시지
            return redirect()->back()->with('msg', '글이 성공적으로 삭제되었습니다.');
        }
    }

}
