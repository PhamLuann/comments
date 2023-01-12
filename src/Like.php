<?php

namespace Laravelista\Comments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Like extends Model{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'comment_id'
    ];
    public function comment(){
        return $this->belongsTo(Config::get('comments.model'));
    }

    public function user(){
        return $this->belongsTo(Config::get('comments.user'));
    }
}