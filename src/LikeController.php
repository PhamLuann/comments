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
        $Like = Config::get('comments.like');
        $this->like = new $Like;
    }

    public function like(Request $request){
        dd(Auth::user());
        $this->like->create([
            'user_id' => auth()->user()->id,
            'comment_id' => $request->get('comment_id'),
        ]);
        return redirect()->back();
    }
}