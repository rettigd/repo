<?php

namespace App\Repositories;


use App\Models\Post;

class PostRepository extends Repository
{

    const ADMIN_VISIBLE = ['subject', 'body', 'is_approved', 'user_id', 'user', 'comments'];
    const OWNER_VISIBLE = ['subject', 'body', 'is_approved', 'user_id', 'user', 'comments'];
    const APPROVED_VISIBLE = ['subject', 'body', 'comments'];

    const ADMIN_FILLABLE = ['subject', 'body', 'is_approved', 'user_id'];
    const USER_FILLABLE = ['subject', 'body', 'user_id'];

    protected $with = ['user','comments.user', 'comments.post.comments'];

    public function create($attributes)
    {
        return parent::create($this->mergeDefaults($attributes));
    }

    public function update($id, $attributes)
    {
        return parent::create($this->mergeDefaults($id, $attributes));
    }

    public function getVisible()
    {
        if ($this->user->is_admin) {
            return PostRepository::ADMIN_VISIBLE;
        }

        if ($this->user->id == $this->model->user_id) {
            return PostRepository::OWNER_VISIBLE;
        }

        if ($this->model->is_approved) {
            return PostRepository::APPROVED_VISIBLE;
        }

        return [];
    }

    public function getFillable(array $attributes = null)
    {
        if ($this->model->id) {
            // Update Fillable i.e. remove the ability to update a columns after a certain value is set
        }
        if ($this->user->is_admin) {
            return PostRepository::ADMIN_FILLABLE;
        }

        return PostRepository::USER_FILLABLE;
    }

    public function mergeDefaults(array $attributes)
    {
        return array_merge($attributes, ['user_id' => $this->user->id]);
    }

}