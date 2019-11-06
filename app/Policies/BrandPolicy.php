<?php

namespace App\Policies;

use App\User;
use App\Brand;

class BrandPolicy extends Policy
{
    /**
     * Determine if the user is allowed to store.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function store(User $user)
    {
        return true;
    }

    /**
     * Determine if the user is allowed to update.
     *
     * @param  \App\User  $user
     * @param  \App\Brand  $message
     * @return bool
     */
    public function update(User $user, Brand $brand)
    {
        return $user->id === $brand->seller_id;
        // return !!$user->brands()->find($brand->id);
    }
}
