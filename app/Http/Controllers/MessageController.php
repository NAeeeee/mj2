<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Log;
use DB;
use Carbon\Carbon;

class MessageController extends Controller
{
     // 받은 쪽지함
    public function inbox(Request $request)
    {
        Log::info(__METHOD__);

        $div = [];
        $div = $request->div ?? '';
        log::info('div : '.$div);
        log::info('id : '.auth()->id());
        $message = [];
        $query = DB::table('messages');

        if( $div === 'S' )
        {
            $query->where('sender_id', auth()->id())
                    ->where('div','S');
        }
        else if ($div === 'R') 
        {
            $query->where('receiver_id', auth()->id())
                    ->where('div','R');
        }
        else
        {
            // 관리자는 전체에서는 2개 다 보여야함
            if (array_key_exists(auth()->user()->id, config('var.admin'))) 
            {
                $query->where(function($q) {
                    $q->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
                });
            }
            else
            {
                // div 없을 때 (전체 탭 등) → 사용자에게는 중복 방지!
                // 예: 받은 메시지 위주로 보여주기
                $query->where('receiver_id', auth()->id())
                        ->where('div','R');
            }
        }
        $query->where('save_status','Y');

        $message = $query
                ->orderby('no','asc')
                ->paginate(5)
                ->withQueryString();

        $message->transform(function ($msg) {
            if( $msg->sender_id = 2 ) // 관리자라면
            {
                $msg->sender_id = '관리자';
            }
            // $msg->created_at = Carbon::parse($msg->created_at)->format('Y-m-d');

            return $msg;
        });

        return view('message.inbox', compact('message', 'div'));
    }


    public function show($no)
    {
        Log::info(__METHOD__);

        $message = [];
        $message = Message::where('no', $no)
                ->first();

        if( isset($message->sender_id) && $message->sender_id = 2 ) // 관리자라면
        {
            $message->sender_id = '관리자';
        }

        // 읽음표시 업데이트
        $msg = Message::findOrFail($no);

        // 데이터 수정
        $msg->is_read = 1;
        $msg->save();

        return view('message.show', compact('message'));
    }
}
