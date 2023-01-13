<?php

namespace Laravelista\Comments;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class LikeController extends Controller{
    private $like;

    public function __construct()
    {
        $this->middleware('web');
        $Like = Config::get('comments.like');
        $this->like = new $Like;
    }

    public function doLike(Request $request){
        $comment_id = $request->get('comment_id');
        $exist = (new static())::check($comment_id);
        if($exist){
            $this->unLike($comment_id);
            return redirect()->back();
        }else{
            $this->like($comment_id);
            return redirect()->back();
        }
    }
    public static function check($comment_id){
        $exist = (new static())->like->where([
            'user_id' => Auth::id(),
            'comment_id' => $comment_id,
        ])->first();
        return $exist ? true : false;
    }
    public function like($comment_id){
        $this->like->create([
            'user_id' => Auth::id(),
            'comment_id' => $comment_id,
        ]);
    }

    public function unLike($comment_id){
        $this->like->where([
            'user_id' => Auth::id(),
            'comment_id' => $comment_id
        ])->delete();
    }

    public function viewUserLike(Request $request){
        $commentClass = Config::get('comments.model');
        $comment = $commentClass::find($request->comment_id);
        $likes = $comment->like()->get();
        $output =
            '<div class="relative w-4/5 md:w-1/2 xl:w-1/3 bg-sky-300 shadow-xl shadow-indigo-500/50 rounded-lg px-5 py-2" id="view-user-like">
                <button class="absolute right-2 z-40 hover:text-red-500" onclick="close_user_like()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h1 class="font-bold mt-2">User like</h1> <hr>
                <div class="flex justify-center">
                <div>';
        foreach ($likes as $like){
            $output .=
                '<div class="flex items-center mt-2"><img class="w-6 h-6 rounded-full"
             src="https://www.gravatar.com/avatar/{{ md5($comment->commenter->email ?? $comment->guest_email) }}.jpg?s=64"
             alt="{{ $comment->commenter->name ?? $comment->guest_name }} Avatar">';
             $output .= "<h5 class='ml-3 capitalize'>{$like->user()->first()->name}</h5></div>";
        }
        $output .= '</div></div></div>';

        echo json_encode($output);
    }
}