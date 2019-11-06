<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the doctor that owns the work schedule.
     */
    public function doctor()
    {
        return $this->belongsTo('App\User', 'doctor_id');
    }
}
