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
        else
        {
            $query->where('receiver_id', auth()->id())
                    ->where('div','R');
        }
        $query->where('save_status','Y');

        $message = $query
                ->orderby('no','desc')
                ->paginate(5)
                ->withQueryString();

        $message->transform(function ($msg) use ($div) {

            $user = User::find($msg->sender_id);

            if ( $user && $user->is_admin === 'Y' ) 
            {
                $msg->name = '관리자';
            }
            else
            {
                $msg->name = $user?->name ?? '';
            }
            
            // 보낸쪽지함
            if( isset($div) && $div === 'S' )
            {
                $user_r = User::find($msg->receiver_id);

                if( $user_r && $user_r->is_admin === 'Y' ) 
                {
                    $msg->name_r = '관리자';
                }
                else
                {
                    $msg->name_r = $user_r?->name ?? '';
                }
            }

            return $msg;
        });

        return view('message.inbox', compact('message', 'div'));
    }


    public function show($no)
    {
        Log::info(__METHOD__);

        $message = [];
        $message = Message::where('no', $no)
                ->where('save_status','Y')
                ->first();

        if ( !$message ) 
        {
            abort(404);
        }

        // 보낸 사람, 받는 사람아니면 접근금지
        if( !in_array(auth()->user()->id, [$message->sender_id, $message->receiver_id]) )
        {
            abort(403);
        }

        $user = User::find($message->sender_id);
        if( $user && $user->is_admin === 'Y' ) // 관리자라면
        {
            $message->name = '관리자';
        }
        else
        {
            $message->name = $user->name;
        }

 
        // 접근자가 받는사람일때만 읽음표시 업데이트
        if( auth()->user()->id === $message->receiver_id )
        {
            Message::where('post_no', $message->post_no)
                            ->where('save_status', 'Y')
                            ->update(['is_read' => 1]);
        }
        else
        {
            log::info('접근자 id = '.auth()->user()->id);
            log::info('receiver_id = '.$message->receiver_id);
        }

        // 게시물 상태 조회
        $postSta = \App\Models\Post::where('no', $message->post_no)
                        ->where('save_status', 'Y')
                        ->first();
        
        if( isset($postSta->status) && $postSta->status == 'D' )
        {
            $message->status = 'Y';
        }

        return view('message.show', compact('message'));
    }
}
