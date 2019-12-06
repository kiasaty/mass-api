<?php

namespace App\Policies;

use App\User;

class UserPolicy extends Policy
{
    /**
     * Determine if the user is allowed to get Admins.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function indexAdmins(User $user)
    {
        return false;
    }

    /**
     * Determine if the user is allowed to get Doctors.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function indexDoctors(User $user)
    {
        return true;
    }

    /**
     * Determine if the user is allowed to get Secretaries.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function indexSecretaries(User $user)
    {
        return $user->isDoctor();
    }

    /**
     * Determine if the user is allowed to get Patients.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function indexPatients(User $user)
    {
        return $user->isDoctor() || $user->isSecretary();
    }

    /**
     * Determine if the user is allowed to get a related User.
     * 
     * The authenticated user should be the same as the requester user.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function relatedUser(User $user, $requesterUser)
    {
        return $user->id == $requesterUser->id;
    }

    /**
     * Determine if the user is allowed to store.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function store(User $user)
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
        if ($user->id == $requestedUser->id) {
            return true;
        }

        if ($user->isDoctor()) {
            return  !!$user->patients()->find($requestedUser->id) ||    // requestedUser is the doctor's patient
                    !!$user->secretaries()->find($requestedUser->id);   // requestedUser is the doctor's secretary
        }

        if ($user->isSecretary()) {
            return  !!$user->patients()->find($requestedUser->id) ||    // requestedUser is the secretary's patient
                    !!$user->doctors()->find($requestedUser->id);       // requestedUser is the secretary's doctor
        }

        return false;
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