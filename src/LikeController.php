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

    public function like(Request $request){
        $this->like->create([
            'user_id' => Auth::id(),
            'comment_id' => $request->get('comment_id'),
        ]);
        return redirect()->back();
    }

    public function unLike(Request $request){
        $this->like->where([
            'user_id' => Auth::id(),
            'comment_id' => $request->get('comment_id')
        ])->delete();
        return redirect()->back();
    }


}