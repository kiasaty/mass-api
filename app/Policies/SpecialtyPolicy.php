<?php

namespace App\Policies;

use App\User;

class SpecialtyPolicy extends Policy
{
    /**
     * Determine if the user is allowed to create or edit new specialty.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function storeOrUpdate(User $user)
    {
        return false;
    }

    /**
     * Determine if the doctor is allowed to add or remove the specialty.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function addOrRemove(User $user)
    {
        return $user->isDoctor();
    }
}
