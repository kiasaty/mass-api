<?php

namespace App\Policies;

use App\User;

class MedicinePolicy extends Policy
{
    /**
     * Determine if the user is allowed to create or edit new medicine.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function storeOrUpdate(User $user)
    {
        return false;
    }
}
