<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the user can view the modelsComment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $modelsComment
     * @return mixed
     */
    public function view(User $user, Comment $comment)
    {

        return $user->is_admin || $user->id == $comment->user_id || $comment->is_approved || $comment->post->user_id == $user->id ||
            $comment->user->comments()->whereHas('user', function($query) use ($user) {
                $query->whereUserId($user->id);
            })->count();
    }


    /**
     * Determine whether the user can create modelsComments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the modelsComment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $modelsComment
     * @return mixed
     */
    public function update(User $user, Comment $comment)
    {
        return $user->is_admin || $user->id == $comment->user_id;
    }

    /**
     * Determine whether the user can delete the modelsComment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $modelsComment
     * @return mixed
     */
    public function delete(User $user, Comment $comment)
    {
        return $user->is_admin;
    }
}
