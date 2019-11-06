<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the doctors that has this spacialty.
     */
    public function doctors()
    {
        return $this->belongsToMany('App\User', null, 'doctor_id');
    }
}
