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
                ->count();

        $perPage = 5;
        $totalPages = ceil($totalCnt / $perPage);

        $page = [
            'totalCnt' => $totalCnt,
            'totalPages' => $totalPages,
        ];

        return view('notice.index', compact('notice', 'page'));
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
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // 최대 2MB
        ]);

        $notice = \App\Models\Notice::create([
            'title' => $request->title,
            'content' => $request->content,
            'div' => $request->div,
            'save_id' => auth()->id(),
            'save_status' => 'Y',
        ]);

        $createdNo = $notice->no;

        if ($request->hasFile('file')) {
            log::info("NoticeController 파일있음");
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

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
                $file->move($fullPath, $saveName);

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
        ]);


        return redirect()->route('notice.show', $notice->no)
                         ->with('success', '게시글이 수정되었습니다.');
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
        $notice->save();

        return redirect()->route('notice.index')
                ->with('msg', '글이 성공적으로 삭제되었습니다.');
    }
}
