<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
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
            'type'                  => 'WorkSchedule',

            // Attributes
            'id'                    => $this->id,
            'medical_record_number' => $this->medical_record_number,

            // Relationships
            'patient'               => new UserResource($this->patient),
        ];
    }
}
