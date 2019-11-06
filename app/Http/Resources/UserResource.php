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
            'type'              => $this->role,

            // Attributes
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'phone_number'      => $this->phone_number,
            'username'          => $this->when($request->user()->isAdmin(), $this->username),
            'profile_photo'     => $this->profile_photo,

            // Relationships
            'secretary'         => $this->when($this->isDoctor(), new self($this->secretary)),
            'specialties'       => $this->when($this->isDoctor(), new self($this->specialties)),
            'doctors'           => $this->when($this->isSecretary(), self::collection($this->doctors)),
            'medical_record'    => $this->when($this->isPatient(), MedicalRecordResource::collection($this->medicalRecords)),
        ];
    }
}
