<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * The additional meta data that should be added to the resource response.
     *
     * Added during response construction by the developer.
     *
     * @var array
     */
    public $additional = ['success' => true];
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type'          => $this->role,
            
            // Attributes
            'id'            => $this->id,
            'firstname'     => $this->firstname,
            'lastname'      => $this->lastname,
            'fullname'      => $this->fullname,
            'phone_number'  => $this->phone_number,
            'username'      => $this->username,
            'profile_photo' => $this->profile_photo,

            // Relationships
            'medical_record_number' => $this->when($this->isPatient(), function () {
                return $this->medicalRecord->medical_record_number;
            }),
            'secretaries'   => $this->when($this->isDoctor(), UserResource::collection($this->secretaries)),
            'specialties'   => $this->when($this->isDoctor(), SpecialtyResource::collection($this->specialties)),
            'workScedules'  => $this->when($this->isDoctor(), WorkScheduleResource::collection($this->workSchedules)),
            // 'doctors'       => $this->when($this->isSecretary(), UserResource::collection($this->doctors)),
        ];
    }
}
