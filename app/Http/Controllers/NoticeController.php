<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use DB;
use App\Models\User;
use App\Models\PostFile;
use App\Models\Notice;
use Carbon\Carbon;

class NoticeController extends Controller
{
    // 공지사항 메인
    public function index(Request $request)
    {
        Log::info(__METHOD__);

        $page = $request->page ?? 1;
        $div = $request->div ?? '';
        $search_div = $request->search_div ?? '';
        $keyword = $request->search ?? '';

        $notice = [];

        $query = DB::table('notice')
                ->where('save_status', 'Y')
                ->where('is_visible', 'Y')  // 노출여부
                ->orderby('no','desc');

        if ($search_div && $keyword) 
        {
            // 제목
            if ($search_div === 'title') 
            {
                $query->where('title', 'like', "%{$keyword}%");
            }
            // 내용
            elseif ($search_div === 'content') 
            {
                $query->where('content', 'like', "%{$keyword}%");
            }
        }

        $notice = $query->paginate(5)->withQueryString();

        $notice->transform(function ($nn) {
                $notice_div = config('var.notice_div');
                $nn->created_at = Carbon::parse($nn->created_at)->format('Y-m-d');

                $nn->div = $notice_div[$nn->div];

                return $nn;
            });    
        
        $totalCnt = DB::table('notice')
                ->where('save_status', 'Y')
                ->where('is_visible', 'Y')
                ->count();

        $perPage = 5;
        $totalPages = ceil($totalCnt / $perPage);

        $page = [
            'totalCnt' => $totalCnt,
            'totalPages' => $totalPages,
        ];

        return view('notice.index', compact('notice', 'page'));
    }

    public function list(Request $request)
    {
        Log::info(__METHOD__);

        $search_div = $request->search_div ?? '';
        $keyword = $request->search ?? '';
        $div = $request->div ?? '';

        $query = DB::table('notice')
                ->orderby('no','asc');

        if( $div )
        {
            if( $div === 'Y' )
            {
                $query->where('save_status', 'Y')
                        ->where('is_visible', 'Y');
            }
            else
            {
                $query->where('is_visible', 'N');
            }
        }

        if ( $search_div && $keyword ) 
        {
            // 제목
            if ( $search_div === 'title' ) 
            {
                $query->where('title', 'like', "%{$keyword}%");
            }
            // 내용
            elseif ( $search_div === 'content' ) 
            {
                $query->where('content', 'like', "%{$keyword}%");
            }
        }

        $notice = $query->paginate(5)->withQueryString();

        $notice->transform(function ($nn) {
            $user = User::find($nn->save_id);
            $notice_div = config('var.notice_div');

            $nn->div = $notice_div[$nn->div];
            $nn->save_name = $user->name;
            $nn->ss = $nn->save_status === 'Y' ? '저장' : '삭제';
            $nn->iv = $nn->is_visible === 'Y' ? '노출' : '비노출';

            return $nn;
        });

        $sta = [
                'all_cnt' => Notice::count(),
                'y_cnt' => Notice::where('save_status', 'Y')
                                    ->where('is_visible','Y')
                                    ->count(),
                'n_cnt'  => Notice::where('is_visible','N')
                                        ->count(),
        ];

        log::info($sta);

        return view('notice.list', compact('notice', 'sta'));
    }

    public function updateStatus(Request $request, $no)
    {
        Log::info(__METHOD__);

        $request->validate([
            'status' => 'required|in:Y,N', // 허용된 값만 통과
        ]);

        if (auth()->user()->is_admin !== 'Y') 
        {
            abort(403);
        }

        $nn = Notice::findOrFail($no);
        $nn->is_visible = $request->is_visible;
        $nn->save();

        log::info($no."번 공지 상태 변경 : ".$request->is_visible);

        return redirect()->route('notice.index')
                            ->with('title_d', '완료')
                            ->with('msg_p2', '노출 여부가 변경되었습니다.');
    }

    // 글 쓰기
    public function create(Request $request)
    {
        Log::info(__METHOD__);

        // 관리자인지 체크
        if( !array_key_exists(auth()->id(), config('var.admin')) )
        {
            abort(403);
        }

        return view('notice.create');
    }

    // 게시물 저장
    public function store(Request $request)
    {
        Log::info(__METHOD__);

        // 관리자인지 체크
        if( !array_key_exists(auth()->id(), config('var.admin')) )
        {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'div' => 'required|string',
            'is_visible' => 'required|string',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // 최대 2MB
        ]);

        $notice = \App\Models\Notice::create([
            'title' => $request->title,
            'content' => $request->content,
            'div' => $request->div,
            'save_id' => auth()->id(),
            'save_status' => 'Y',
            'is_visible' => $request->is_visible,
        ]);

        $createdNo = $notice->no;

        if ($request->hasFile('file')) {
            log::info("NoticeController 파일있음");
            foreach ($request->file('file') as $file) {

                if ($file && $file->isValid()) {
                    $filename = $file->getClientOriginalName();
                    log::info("파일명 : ".$filename);

                    // 1. 경로: storage/app/public/img/날짜 (Laravel 기준)
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
                    $postFile->target_no = $createdNo; // posts 기본키가 no임에 유의
                    $postFile->filename = $filename;
                    $postFile->filepath = $fullPath;
                    $postFile->savename = $saveName;
                    $postFile->filesize = $size;
                    $postFile->filetype = $file->getClientMimeType();
                    $postFile->extension = $file->getClientOriginalExtension();
                    $postFile->save_status = 'Y';
                    $postFile->target_type = 'N';
                    $postFile->save();

                    Log::info("파일 저장 db 성공");
                }

            }
        };

        return redirect()->route('notice.show', ['no' => $notice->no])
                ->with('msg', '글이 작성되었습니다!');
    }


    public function show($no)
    {
        Log::info(__METHOD__);

        $notice = Notice::findOrFail($no);

        // 사진
        $img = [];
        $img = DB::table('post_file')
                ->where('target_no', $no)
                ->where('target_type','N')
                ->where('save_status', 'Y')
                ->orderby('no','asc')
                ->get();

                // 날짜 추출
        $img->transform(function ($i) {
            $i->pathDate = explode('_', $i->savename)[0]; // "20250604"

            return $i;
        });

        return view('notice.show', compact('notice', 'img'));
    }


    public function edit($no)
    {
        Log::info(__METHOD__);

        // 관리자인지 체크
        if( !array_key_exists(auth()->id(), config('var.admin')) )
        {
            abort(403);
        }

        $notice = Notice::findOrFail($no);

        $img = [];
        $img = DB::table('post_file')
                ->where('target_no', $no)
                ->where('target_type','N')
                ->where('save_status', 'Y')
                ->orderby('no','asc')
                ->get();

                // 날짜 추출
        $img->transform(function ($i) {
            $i->pathDate = explode('_', $i->savename)[0]; // "20250604"

            return $i;
        });

        return view('notice.edit', compact('notice' ,'img'));
    }


    // 공지사항 수정 처리
    public function update(Request $request, $no)
    {
        Log::info(__METHOD__);

        // 관리자인지 체크
        if( !array_key_exists(auth()->id(), config('var.admin')) )
        {
            abort(403);
        }

        $notice = Notice::findOrFail($no);

        $request->validate([
            'div' => 'required',
            'save_status' => 'required',
            'is_visible' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // 저장상태 'N'인데 활성화 'N' 불가
        if( $request->save_status == 'N' && $request->is_visible == 'Y' )
        {
            return redirect()->back()
                ->with('title_d', '확인 요청')
                ->with('msg_d', '삭제 상태인 글은 노출 상태로 변경할 수 없습니다.');
        }

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
                $postFile->target_no = $notice->no;
                $postFile->filename = $filename;
                $postFile->filepath = $fullPath;
                $postFile->savename = $saveName;
                $postFile->filesize = $size;
                $postFile->filetype = $file->getClientMimeType();
                $postFile->extension = $file->getClientOriginalExtension();
                $postFile->save_status = 'Y';
                $postFile->target_type = 'N';
                $postFile->save();

                Log::info("파일 저장 db 성공");

            }
        }
        
        Notice::where('no', $no)->update([
            'div' => $request->input('div'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'save_status' => $request->input('save_status'),
            'is_visible' => $request->input('is_visible'),
        ]);


        return redirect()->route('notice.show', $notice->no)
                         ->with('msg', '공지 게시글이 수정되었습니다.');
    }

    public function delete(Request $request, $no)
    {
        Log::info(__METHOD__);

        // 관리자인지 체크
        if( !array_key_exists(auth()->id(), config('var.admin')) )
        {
            abort(403);
        }

        $notice = Notice::findOrFail($no);

        // 데이터 수정
        $notice->save_status = 'N';
        $notice->is_visible = 'N';
        $notice->save();

        return redirect()->route('notice.index')
                ->with('msg', '글이 성공적으로 삭제되었습니다.');
    }
}
