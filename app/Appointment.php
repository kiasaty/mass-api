<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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
        return $this->belongsToMany('App\Medicine');
    }

    /**
     * Gets the experiments associated with this appointment.
     */
    public function experiments()
    {
        return $this->belongsToMany('App\Experiment');
    }
}
