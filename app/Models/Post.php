<?php

namespace App\Models;

use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    public $repositoryClass = PostRepository::class;

    protected $fillable = ['subject', 'body', 'user_id'];

    protected $hidden = ['subject', 'body', 'is_approved', 'user_id', 'user', 'comments', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
