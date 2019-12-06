<?php

namespace App\Policies;

use App\User;
use App\WorkSchedule;

class WorkSchedulePolicy extends Policy
{
    /**
     * Determine if the user is allowed to get Admins.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->isDoctor();
    }

    /**
     * Determine if the user is allowed to create new work schedule.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function store(User $user)
    {
        return $user->isDoctor();
    }

    /**
     * Determine if the user is allowed to update the work schedule.
     *
     * @param  \App\User  $user
     * @param  \App\WorkSchedule  $workSchedule
     * @return bool
     */
    public function update(User $user, WorkSchedule $workSchedule)
    {
        return $user->id == $workSchedule->doctor_id;
    }
}
