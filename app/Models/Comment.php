<?php

namespace App\Models;

use App\Repositories\CommentRepository;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $repositoryClass = CommentRepository::class;

    protected $hidden = ['id', 'post_id', 'subject', 'body', 'is_approved', 'user_id', 'user', 'post', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
