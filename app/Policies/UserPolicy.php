<?php

namespace App\Policies;

use App\User;

class UserPolicy extends Policy
{
    /**
     * Determine if the user is allowed to get users.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function index(User $user)
    {
        return false;
    }

    /**
     * Determine if the user is allowed get the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $requestedUser
     * @return bool
     */
    public function show(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine if the user is allowed to update the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $requestedUser
     * @return bool
     */
    public function update(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine if the user is allowed to delete the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $requestedUser
     * @return bool
     */
    public function destroy(User $user, User $requestedUser)
    {
        return false;
    }
}