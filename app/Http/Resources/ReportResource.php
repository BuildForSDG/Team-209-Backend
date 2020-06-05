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
            ],
            'relationships' => [
                'user' => [
                    'links' => [
//                        'self'    => route('reports.relationships.user', ['id' => $this->id]),
                        'related' => route('reports.users', ['report' => $this->id]),
                    ],
                    'data' => new UserIdentifierResource($this->whenLoaded("user"))
                ],
                'incident' => [
                    'links' => [
//                        'self'    => route('reports.relationships.user', ['id' => $this->id]),
                        'related' => route('reports.incidents', ['report' => $this->id]),
                    ],
                    'data' => new IncidentIdentifierResource($this->whenLoaded("incident"))
                ],
                'reports_attachments' => [
                    'links' => [
                        'self'    => route('reports.relationships.attachments', ['report' => $this->id]),
                        'related' => route('reports.attachments', ['report' => $this->id]),
                    ],
                    'data' => ReportAttachmentIdentifierResource::collection($this->whenLoaded('attachments')),
                ],
            ]
        ];
    }

    private function relations()
    {
        return [
//            new UserResource($this->whenLoaded('user')),
//            new IncidentResource($this->whenLoaded('incident')),
            ReportAttachmentResource::collection($this->whenLoaded('attachments')),
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
        return collect($this->relations())
            ->filter(function ($resource) {
                return $resource->collection !== null;
            })->flatMap->toArray($request);
    }
}
