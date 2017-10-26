<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    const ADMIN_HIDDEN = [];
    const OWNER_HIDDEN = ['password', 'remember_token'];
    const HAS_RELATIONSHIP_HIDDEN = ['email', 'password', 'remember_token', 'is_admin', 'created_at', 'updated_at', 'posts', 'comments'];

    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $userModel
     * @return mixed
     */
    public function view(User $user, User $userModel)
    {

        auth_collection(collect($userModel->getRelations()));

        if ($user->is_admin) {
            $userModel->setHidden(UserPolicy::ADMIN_HIDDEN);
            return true;
        } elseif ($user->id == $userModel->id) {
            $userModel->setHidden(UserPolicy::OWNER_HIDDEN);
            return true;
        } elseif ($userModel->comments()->whereHas('post', function($query) use ($user) {
            $query->whereUserId($user->id);
        })->count()) {
            $userModel->setHidden(UserPolicy::HAS_RELATIONSHIP_HIDDEN);
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create modelsUsers.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the modelsUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $userModel
     * @return mixed
     */
    public function update(User $user, User $userModel)
    {
        //
    }

    /**
     * Determine whether the user can delete the modelsUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $userModel
     * @return mixed
     */
    public function delete(User $user, User $userModel)
    {
        //
    }
}
