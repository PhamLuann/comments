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

        return response()->json(['ok'=>$request->comment_id]);
    }
}