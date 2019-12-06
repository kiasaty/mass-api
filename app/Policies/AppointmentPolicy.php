<?php

namespace App\Policies;

use App\User;
use App\Appointment;

class AppointmentPolicy extends Policy
{
    /**
     * Determine if the user is allowed see the appointment.
     * 
     * Only the doctor and the patient associated with the appointment and the doctor's secretaries can see the appointment.
     *
     * @param  \App\User  $user
     * @param  \App\Appointment  $appointment
     * @return bool
     */
    public function show(User $user, Appointment $appointment)
    {
        return  in_array($user->id, [$appointment->doctor_id, $appointment->patient_id] ) ||
                !!$appointment->doctor->secretaries()->find($user->id);
    }

    /**
     * Determine if the user is allowed to update the appointment.
     * 
     * Only the doctor or his/her secretary can update an appointment.
     *
     * @param  \App\User  $user
     * @param  \App\Appointment  $appointment
     * @return bool
     */
    public function update(User $user, Appointment $appointment)
    {
        return  $user->id == $appointment->doctor_id || 
                !!$appointment->doctor->secretaries()->find($user->id);
    }

    /**
     * Determine if the user is allowed to delete the appointment.
     *
     * @param  \App\User  $user
     * @param  \App\User  $requestedUser
     * @return bool
     */
    public function destroy(User $user, Appointment $appointment)
    {
        return false;
    }

    /**
     * Determine if the doctor is allowed to add or remove the medicine.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function addOrRemoveMedicineOrExperiment(User $user, Appointment $appointment)
    {
        return $user->id == $appointment->doctor_id;
    }

    /**
     * Determine if the user is allowed to update doctor's diagnosis for an appointment.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function updateDoctorDiagnosis(User $user, Appointment $appointment)
    {
        return $user->id == $appointment->doctor_id;
    }
}
