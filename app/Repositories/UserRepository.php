<?php

namespace App\Repositories;


use App\Models\User;

class UserRepository extends Repository
{

    const ADMIN_HIDDEN = [];
    const OWNER_HIDDEN = ['password', 'remember_token'];
    const HAS_RELATIONSHIP_HIDDEN = ['email', 'password', 'remember_token', 'is_admin', 'created_at', 'updated_at', 'posts', 'comments'];


    protected $with = [];

    public function create($attributes)
    {
        parent::create($this->mergeDefaults($attributes));
    }

    public function update($id, $attributes)
    {
        parent::create($this->mergeDefaults($id, $attributes));
    }

    public function getVisible(User $user = null)
    {
        if ($user == null) {
            $user = $this->model;
        }

        if ($this->user->is_admin) {
            return UserRepository::ADMIN_HIDDEN;
        }

        if ($this->user->id == $user->id) {
            return UserRepository::OWNER_HIDDEN;
        }

        if ($user->comments()->whereHas('post', function($query) use ($user) {
            $query->whereUserId($user->id);
        })->count()) {
            return UserRepository::HAS_RELATIONSHIP_HIDDEN;
        }

        return [];
    }

    public function getFillable(array $attributes = null)
    {
        return [];
    }

    public function mergeDefaults(array $attributes)
    {
        return array_merge($attributes, []);
    }

}