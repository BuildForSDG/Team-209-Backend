<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
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
     * @param  Request  $request
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
                'area'         => $this->area,
                'deactivated'  => $this->deactivated_at,
                'created_at'   => $this->created_at,
                'updated_at'   => $this->updated_at,
            ],
            'relationships' => [
                'reports' => [
                    'links' => [
                        'self'    => route('incidents.relationships.reports', ['incident' => $this->id]),
                        'related' => route('incidents.reports', ['incident' => $this->id]),
                    ],
                    'data' => ReportIdentifierResource::collection($this->whenLoaded('reports')),
                ],
            ]
        ];
    }

    private function relations()
    {
        return [
            ReportResource::collection($this->whenLoaded('reports')),
        ];
    }

    public function with($request)
    {
        $with = [];
        if ($this->included($request)->isNotEmpty()) {
            $with['included'] = $this->included($request);
        }
        return $with;
    }

    public function included($request)
    {
        /** @phpstan-ignore-next-line */
        return collect($this->relations())->filter(function ($resource) {
                return $resource->collection !== null;
        })->flatMap->toArray($request);
    }
}
