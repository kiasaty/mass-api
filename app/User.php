<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
    * Users' roles
    * 
    * @var array
    */
    private const ROLES = [
       1 => 'admin',
       2 => 'doctor',
       3 => 'secretary',
       4 => 'patient',
    ];
    
    /**
    * Gets the user's role
    * 
    * @return string role
    */
    public function getRoleAttribute()
    {
       return self::ROLES[ $this->attributes['role_id'] ];
    }
    /**
    * Sets the user's role
    * 
    * @param string role
    */
    public function setRoleAttribute($value)
    {
       $roleID = self::getRoleID($value);
       if ($roleID) {
          $this->attributes['role_id'] = $roleID;
       }
    }
    /**
    * Gets the user's role id
    *
    * Usage: $user->role_id
    * 
    * @return int user's role id
    */
    public function getRoleIdAttribute()
    {
       return $this->attributes['role_id'];
    }

    /**
    * Gets the id of a given role
    *
    * @param string $role  user's role
    * @return int roleID
    */
    public static function getRoleID($role)
    {
       return array_search($role, self::ROLES);
    }
    
    /**
    * Gets the user's full name.
    *
    * there is no full_name in the database, 
    * this method generates the full name.
    * usage: $user->full_name
    *
    * @return string
    */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Checks if the user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role == 'admin';
    }

    /**
     * Checks if the user is doctor
     *
     * @return bool
     */
    public function isDoctor()
    {
        return $this->role == 'doctor';
    }

    /**
     * Checks if the user is secretary
     *
     * @return bool
     */
    public function isSecretary()
    {
        return $this->role == 'secretary';
    }

    /**
     * Checks if the user is patient
     *
     * @return bool
     */
    public function isPatient()
    {
        return $this->role == 'patient';
    }

    /**
     * Gets a admin/doctor/secretary/patient
     * 
     * @param  integer $id   user's id
     * @param  string $role  user's role
     * @return App\User
     */
    public static function getUser($id, $role)
    {
        return self::where('role_id', self::getRoleID($role) )->findOrFail($id);
    }

    /**
     * Gets the all admins/doctors/secretaries/patients
     * 
     * @param string $role   user's role
     * @return App\User admins/doctors/secretaries/patients
     */
    public static function getAll($role)
    {
        return self::where('role', self::getRoleID($role) )->get();
    }

    /**
     * Gets the patient's Medical Record
     */
    public function medicalRecord()
    {
        if ($this->isPatient) {
            return $this->hasOne('App\MedicalRecord', 'patient_id');
        }
    }

    /**
     * Gets the doctor's work schedule.
     */
    public function doctor()
    {
        if ($this->isDoctor) {
            return $this->belongsTo('App\User', 'doctor_id');
        }
    }

    /**
     * Gets the doctor's specialties
     */
    public function specialties()
    {
        if ($this->isDoctor) {
            return $this->belongsToMany('App\Specialty', null, 'doctor_id');
        }
    }

    /**
     * Gets the secetary associated with the doctor
     */
    public function secretary()
    {
        if ($this->isDoctor) {
            // return $this->hasManyThrough('App\User', 'App\Brand', 'seller_id');
            // return $this->hasMany('App\User', 'patient_id');
        }
    }

    /**
     * Gets the doctors associated with the secretary
     */
    public function doctors()
    {
        if ($this->isSecretary) {
            // return $this->hasManyThrough('App\User', 'App\Brand', 'seller_id');
            // return $this->hasMany('App\User', 'patient_id');
        }
    }
}
