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
    public const ROLES = [
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
    * Gets the id of a given role
    *
    * @todo use a function to singular words
    * @param string $role  user's role
    * @return int roleID
    */
    public static function getRoleID($role)
    {
        switch ($role) {
            case 'admins':
                $role = 'admin';
                break;
            case 'doctors':
                $role = 'doctor';
                break;
            case 'secretaries':
                $role = 'secretary';
                break;
            case 'patients':
                $role = 'patient';
                break;
        }

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
    public function getFullnameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
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
        return self::where('role_id', self::getRoleID($role) )->get();
    }

    /**
     * Gets the appointments associated with the doctor/patient.
     */
    public function appointments()
    {
        return $this->hasMany('App\Appointment', "{$this->role}_id");
    }

    /**
     * Gets the secetary associated with the doctor
     */
    public function secretaries()
    {
        return $this->belongsToMany('App\User', 'doctor_users', 'doctor_id', 'secretary_id');
    }

    /**
     * Gets the patients associated with the doctor/secretary
     */
    public function patients()
    {
        return $this->belongsToMany('App\User', 'appointments', "{$this->role}_id", 'patient_id');
    }

    /**
     * Gets the doctor's specialties
     */
    public function specialties()
    {
        return $this->belongsToMany('App\Specialty', 'doctor_specialty', 'doctor_id');
    }

    /**
     * Gets the doctor's work schedules
     */
    public function workSchedules()
    {
        return $this->hasMany('App\WorkSchedule', 'doctor_id')
                    ->orderBy('day_of_week')
                    ->orderBy('start_time');
    }

    /**
     * Gets the doctors associated with the secretary
     */
    public function doctors()
    {
        return $this->belongsToMany('App\User', 'doctor_users', 'secretary_id', 'doctor_id');
    }

    /**
     * Gets the patient's Medical Record
     */
    public function medicalRecord()
    {
        return $this->hasOne('App\MedicalRecord', 'patient_id');
    }

    /**
     * Generates MRN for the patient.
     * 
     * @todo check what create method returns
     * @return bool
     */
    public function generateMedicalRecordNumberForPatient()
    {
        $this->medicalRecord()->create([
            'medical_record_number' => time(),
        ]);
    }
}
