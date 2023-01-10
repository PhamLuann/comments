<?php

namespace Laravelista\Comments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Like extends Model{
    use SoftDeletes;

    public function comment(){
        return $this->belongsTo(Config::get('comments.model'));
    }
}