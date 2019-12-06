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
            'type'  => 'Appointment',

            // Attributes
            'id'                => $this->id,
            'start_time'        => $this->start_time->format('Y/m/d H:i'),
            'end_time'          => $this->end_time->format('Y/m/d H:i'),
            'doctor_diagnosis'  => $this->doctor_diagnosis,

            // Relationships
            'doctor'        => new UserResource($this->doctor),
            'patient'       => new UserResource($this->patient),
            'medicines'     => MedicineResource::collection($this->medicines),
            'experiments'   => ExperimentResource::collection($this->experiments),
        ];
    }
}
