<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Post;

class CommentRepository extends Repository
{

    const ADMIN_VISIBLE = ['id', 'post_id', 'subject', 'body', 'is_approved', 'user_id', 'user', 'post', 'created_at', 'updated_at'];
    const OWNER_VISIBLE = ['id', 'post_id', 'subject', 'body', 'is_approved', 'user_id', 'user', 'post', 'created_at', 'updated_at'];
    const POST_OWNER_VISIBLE = ['id', 'post_id', 'subject', 'body', 'is_approved', 'user_id', 'user', 'post', 'created_at', 'updated_at'];
    const APPROVED_VISIBLE = ['id', 'post_id', 'subject', 'body', 'is_approved', 'user', 'post', 'created_at', 'updated_at'];
    const HAS_RELATIONSHIP_VISIBLE = ['user'];

    const ADMIN_FILLABLE = ['post_id', 'subject', 'body', 'is_approved', 'user_id'];
    const POST_OWNER_FILLABLE = ['post_id', 'subject', 'body', 'is_approved', 'user_id'];
    const USER_FILLABLE = ['post_id', 'subject', 'body', 'user_id', ];


    protected $with = ['user'];

    protected $post;

    public function create($attributes)
    {
        $this->post = app()->make(PostRepository::class)->getById($attributes['post_id']);

        if ($this->post) {
            return parent::create($this->mergeDefaults($attributes));
        }

        throw new \Exception();

    }

    public function getVisible(Comment $comment = null)
    {

        if ($comment == null) {
            $comment = $this->model;
        }

        $user = $this->user;

        if ($this->user->is_admin) {
            return CommentRepository::ADMIN_VISIBLE;
        }

        if ($this->user->id == $comment->user_id) {
            return CommentRepository::OWNER_VISIBLE;
        }

        if ($comment->is_approved) {
            return CommentRepository::APPROVED_VISIBLE;
        }

        if ($comment->post->user_id == $this->user->id) {
            return CommentRepository::POST_OWNER_VISIBLE;
        }

        if ($comment->user->comments()->whereHas('user', function($query) use ($user) {
            $query->whereUserId($user->id);
        })->count()) {
            return CommentRepository::HAS_RELATIONSHIP_VISIBLE;
        }

        return [];
    }

    public function getFillable(array $attributes = null)
    {

        if ($this->model->id) {
            //
        }

        if ($this->user->is_admin ) {
            return CommentRepository::ADMIN_FILLABLE;
        }

        if (isset($attributes['post_id'])) {
            $this->post = app()->make(PostRepository::class)->getById($attributes['post_id']);

            if ($this->post->user_id == $this->user->id) {
                return CommentRepository::POST_OWNER_FILLABLE;
            }

        }

        return CommentRepository::USER_FILLABLE;
    }

    public function mergeDefaults(array $attributes)
    {
        if ($this->model->id) {
            //on update
        }
        return array_merge($attributes, ['user_id' => $this->user->id]);
    }

}