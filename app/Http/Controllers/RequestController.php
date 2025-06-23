<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Models\Post;
use App\Models\PostFile;
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
        // 진입 시 견적요청이 있으면 리스트를 보여주고 없다면 작성하기를 보여줌
        Log::info(__METHOD__);
        log::info($request->all());
        // dd('도착함', $request->all());

        // id로
        $posts = DB::table('posts')
                ->where('user_id', $request->id)
                ->where('save_status', 'Y')
                ->orderby('no','asc')
                ->paginate(5)
                ->withQueryString();
        
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


        // return view('request.index')->with('request', $request);
    }

    public function create(Request $request)
    {
        log::info($request);
        if ($request->input('dsdsds') === 'new') {
            // 추가 작성 요청일 때의 로직
        }

        return view('request.index')->with('request', $request);
    }

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

    // 글쓰기
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
            'user_id' => auth()->id() ?? 1, // 로그인 안 됐을 경우 대비 (또는 익명)
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

        return t('/request?id=' . $post->auth()->id())->with('success', '글이 작성되었습니다!');
    }


    public function delete($id)
    {
        Log::info(__METHOD__);

        $board = Post::findOrFail($id);

        // 데이터 수정
        $board->save_status = 'N';
        $board->save();

        // 리다이렉트 및 플래시 메시지
        return redirect('/')->with('success', '글이 삭제 처리되었습니다.');
    }

}
