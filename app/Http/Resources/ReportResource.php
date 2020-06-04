<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ReportResource
 * @package App\Http\Resources
 * @mixin  \App\Report
 */
class ReportResource extends JsonResource
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
            'type'          => 'reports',
            'attributes'    => [
                'address'       => $this->address,
                'description'   => $this->description,
                'latitude'      => strval($this->location->getLat()),
                'longitude'     => strval($this->location->getLng()),
                'created_at'    => $this->created_at,
                'updated_at'    => $this->updated_at,
            ]
        ];
    }
}
