<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the modelsPost.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $modelsPost
     * @return mixed
     */
    public function view(User $user, Post $post)
    {
        return $user->is_admin || $user->id == $post->user_id || $post->is_approved;
    }

    /**
     * Determine whether the user can create modelsPosts.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user, Post $post)
    {
        return true;
    }

    /**
     * Determine whether the user can update the modelsPost.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $modelsPost
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        return $user->id == $post->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can delete the modelsPost.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $modelsPost
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        return $user->id == $post->user->id || $user->is_admin;
    }
}
