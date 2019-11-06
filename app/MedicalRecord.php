<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the patient that owns this medical record.
     */
    public function patient()
    {
        return $this->belongsTo('App\User', 'patient_id');
    }
}
