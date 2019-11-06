<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'type'          => 'Appointment',

            // Attributes
            'id'            => $this->id,
            'start_time'    => $this->start_time,
            'end_time'     => $this->end_time,
            'doctor_diagnosis'  => $this->doctor_diagnosis,

            // Relationships
            'patient'       => new UserResource($this->patient),
            'doctor'        => new UserResource($this->doctor),
        ];
    }
}
