<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class IncidentResource
 * @package App\Http\Resources
 * @mixin  \App\Incident
 */
class IncidentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => strval($this->id),
            'type'          => 'incidents',
            'attributes'    => [
                'address'      => $this->address,
                'location'     => $this->location,
                'area'          => $this->area,
                'deactivated'   => $this->deactivated,
                'created_at'    => $this->created_at,
                'updated_at'    => $this->updated_at,
            ]
        ];
    }
}
