<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\AppointmentService;

class Appointment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Gets the patient associated with this appointment.
     */
    public function patient()
    {
        return $this->belongsTo('App\User', 'patient_id');
    }

    /**
     * Gets the doctor associated with this appointment.
     */
    public function doctor()
    {
        return $this->belongsTo('App\User', 'doctor_id');
    }

    /**
     * Gets the medicines associated with this appointment.
     */
    public function medicines()
    {
        return $this->belongsToMany('App\Medicine')
                        ->withPivot('count')
                        ->withPivot('doctor_order')
                        ->withTimestamps();
    }

    /**
     * Gets the experiments associated with this appointment.
     */
    public function experiments()
    {
        return $this->belongsToMany('App\Experiment')
                    ->withPivot('doctor_order')
                    ->withTimestamps();
    }

    /**
     * Schedules an appointment.
     * 
     * @param  array  $args  includes patient_id and doctor_id
     * @return \App\Appointment
     */
    public static function schedule($args)
    {
        $appointmentService = new AppointmentService([
            'doctor_id'     => $args['doctor_id'],
            'patient_id'    => $args['patient_id']
        ]);

        return $appointmentService->schedule();
    }

    /**
     * Updates doctor's order for a medicine of an appointment.
     * 
     * @param  int   $medicineID
     * @param  array $data  data to be updated
     * @return int
     */
    public function updateMedicineOrder($medicineID, $data)
    {
        return $this->medicines()->updateExistingPivot($medicineID, $data);

    }

    /**
     * Updates doctor's order for a experiment of an appointment.
     * 
     * @param  int   $experimentID
     * @param  array $data  data to be updated
     * @return int
     */
    public function updateExperimentOrder($experimentID, $data)
    {
        return $this->experiments()->updateExistingPivot($experimentID, $data);

    }

    /**
     * Gets User's appointments.
     * 
     * @param  \App\User $user
     * @return \App\Appointment
     */
    public static function getAppointments($user)
    {
        if ($user->isAdmin()) {
            return Appointment::all();
        }

        if ($user->isSecretary()) {
            // return $user->doctors()->appointments;
            return self::whereHas('doctor.secretaries', function ($query) use ($user) {
                $query->where('secretary_id', $user->id);
            })->get();
        }

        return $user->appointments;
    }
}
