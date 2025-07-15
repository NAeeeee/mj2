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
        $this->middleware('is_admin');
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
                    'posts.no', 'posts.title', 'posts.user_id', 'posts.created_at', 'posts.status', 'posts.save_status', 'posts.div', 'posts.confirm_status',
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
                $query->whereNotNull('post_replies.no')
                        ->whereIn('posts.status',['C', 'D']);
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

        
        // 답변완료에서 '고객확인중', '고객확인완료' 구분
        $posts->transform(function ($post) {
            $rdiv = config('var.board_div');
            $post->div = $rdiv[$post->div] ?? $post->div;
                
            if( $post->status === 'D' )
            {
                $post->view_status = $post->confirm_status === 'Y' ? 'DY' : $post->status;
            }
            else
            {
                $post->view_status = $post->status;
            }
                
            return $post;
        });


        // 미답변, 답변완료 cnt
        $sta = [
            'div_ab' => Post::where('save_status', 'Y')
                                    ->whereIn('status',['A','B'])
                                    ->whereHas('user', function ($query) {
                                        $query->where('status', 'Y');
                                    })
                                    ->count(),
            'div_cd' => Post::where('save_status', 'Y')
                                    ->whereIn('status',['C','D'])
                                    ->whereHas('user', function ($query) {
                                        $query->where('status', 'Y');
                                    })
                                    ->count(),
        ];

        return view('boards.index', compact('posts', 'div', 'search_div', 'keyword', 'sta'));
    }


    // 관리자가 견적요청상태 변경
    public function updateStatus(Request $request, $no)
    {
        Log::info(__METHOD__);

        $request->validate([
            'status' => 'required|in:E,Z', // 허용된 값만 통과
        ]);

        if (auth()->user()->is_admin !== 'Y') {
            abort(403);
        }

        $adminId = auth()->user()->id;

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

            log::info('상태 : '.$request->status);

            $url = route('request.show', ['id' => $no]);
            if( $request->status === 'E' ) // 반려
            {
                $div = 'E';
                log::info('상태 : 반려');
                $content = '<a target="_blank" href="' . $url . '">' . $post->title . '</a><br><br>'
                . '요청하신 건은 ' . date('Y년 m월 d일') . '에 <strong>반려 처리</strong>되었습니다.<br>'
                . '보다 나은 안내를 위해 추가 문의가 필요하실 경우 언제든지 문의해 주세요.';

                \App\Models\Message::create([
                    'title' => '반려 처리 안내 [ ' . date('Y-m-d') . ' ]',
                    'sender_id' => $adminId,
                    'receiver_id' => $post->user_id,
                    'div' => 'S',
                    'content' => $content,
                    'is_read' => 0,
                    'post_no' => $post->no,
                    'type' => 'admin_returned',
                    'save_status' => 'Y',
                ]);

                \App\Models\Message::create([
                    'title' => '반려 처리 안내 [ ' . date('Y-m-d') . ' ]',
                    'sender_id' => $adminId,
                    'receiver_id' => $post->user_id,
                    'div' => 'R',
                    'content' => $content,
                    'is_read' => 0,
                    'post_no' => $post->no,
                    'type' => 'admin_returned',
                    'save_status' => 'Y',
                ]);
            }
            else // 처리완료
            {
                log::info('상태 : 처리완료');
                $div = 'Z';
                $content = '요청건은 '
                    . date('Y년 m월 d일') . '에 확인되어 처리 완료되었습니다. <br>'
                    . '이후에도 궁금한 점이 있다면 언제든지 문의해 주세요.';

                \App\Models\Message::create([
                    'title' => '게시물 처리 완료 안내 [ ' . date('Y-m-d') . ' ]',
                    'sender_id' => $adminId,
                    'receiver_id' => $post->user_id,
                    'div' => 'S',
                    'content' => $content,
                    'is_read' => 0,
                    'post_no' => $post->no,
                    'type' => 'admin_done',
                    'save_status' => 'Y',
                ]);

                \App\Models\Message::create([
                    'title' => '게시물 처리 완료 안내 [ ' . date('Y-m-d') . ' ]',
                    'sender_id' => $adminId,
                    'receiver_id' => $post->user_id,
                    'div' => 'R',
                    'content' => $content,
                    'is_read' => 0,
                    'post_no' => $post->no,
                    'type' => 'admin_done',
                    'save_status' => 'Y',
                ]);
            }

            return redirect()->route('boards.index', ['div' => $div])
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

                // 1. 경로: storage/app/public/img/날짜
                $path = 'public/img/' . now()->format('Ymd');

                // 2. 절대경로 구하기(storage/app/public/img/날짜)
                $fullPath = storage_path('app/public/img/' . now()->format('Ymd'));
                Log::info('저장 경로: ' . $fullPath);

                // 파일 사이즈 로그
                $size = $file->getSize();
                Log::info("사이즈 : " . $size);

                // 3. 경로가 없으면 Laravel File 클래스로 생성 (0755 권한, 재귀 생성)
                if (!\File::exists($fullPath)) {
                    \File::makeDirectory($fullPath, 0755, true);
                }

                // 4. 저장할 파일명
                $saveName = now()->format('Ymd_His') . '_' . $filename;
                Log::info("경로 : " . $fullPath);
                Log::info("저장 파일명 : " . $saveName);

                // 5. 이동 (storage/app/public/img/날짜/파일명)
                $file->move($fullPath, $saveName);

                // 6. 웹에서 접근할 URL (public/storage/img/날짜/파일명)
                // 'public/'을 'storage/'로 치환해서 URL 생성
                $url = asset(str_replace('public/', 'storage/', $path . '/' . $saveName));
                Log::info("접근 URL : " . $url);

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

        $content = '요청하신 건에 대해 답변을 남겼습니다.<br> 내용을 확인해주시고, 이상이 없다면 <strong>확인 완료</strong> 버튼을 눌러주세요.<br>'
        . '추가 문의사항이 있으시면 언제든지 추가 문의를 통해 알려주세요.<br><br> 감사합니다.';

        // 보낸 쪽지 (관리자 보낸편지함용)
        \App\Models\Message::create([
            'title' => '게시물 답변 완료 [ '.date('Y-m-d').' ]',
            'sender_id' => $request->admin_id, // DB는 일단 관리자ID
            'receiver_id' => $request->user_id,
            'div' => 'S',                // 알림
            'content' => $content,
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
            'content' => $content,
            'is_read' => 0,
            'post_no' => $request->post_no,
            'save_status' => 'Y',
        ]);

        return redirect()->route('request.show', ['id' => $post->no])
                ->with('msg', '댓글이 작성되었습니다!');
    }

}
