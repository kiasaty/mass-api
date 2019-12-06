<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkScheduleResource extends JsonResource
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
            'type'          => 'WorkSchedule',

            // Attributes
            'id'            => $this->id,
            'day_of_week'   => getDayTitle($this->day_of_week),
            'start_time'    => substr($this->start_time, 0, 5),
            'end_time'      => substr($this->end_time, 0, 5),
        ];
    }
}
