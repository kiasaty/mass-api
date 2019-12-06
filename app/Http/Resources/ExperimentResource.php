<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExperimentResource extends JsonResource
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
            'type'          => 'Experiment',

            // Attributes
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,

            // Pivots
            'doctor_order' => $this->whenPivotLoaded('appointment_experiment', function () {
                return $this->pivot->doctor_order;
            }),
        ];
    }
}
