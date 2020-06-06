<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ReportAttachmentResource
 * @package App\Http\Resources
 * @mixin \App\ReportsAttachment
 */
class ReportAttachmentResource extends JsonResource
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
            'type'          => 'reports_attachments',
            'attributes'    => [
                'type'       => $this->type,
                'file'       => $this->file,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'relationships' => [
                'reports' => [
                    'links' => [
//                        'self'    => route('reports.relationships.user', ['id' => $this->id]),
                        'related' => route('attachments.reports', ['attachment' => $this->id]),
                    ],
                    'data' => new ReportIdentifierResource($this->report)
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
        return collect($this->relations())
            ->filter(function ($resource) {
                return $resource->collection !== null;
            })->flatMap->toArray($request);
    }
}
