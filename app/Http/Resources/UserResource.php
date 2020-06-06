<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @mixin  \App\User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => strval($this->id),
            'type'          => 'users',
            'attributes'    => [
                'name'      => $this->name,
                'email'     => $this->email,
                'type'      => $this->type,
                'image'     => $this->image,
                'created_at'=> $this->created_at,
                'updated_at'=> $this->updated_at,
            ],
            'relationships' => [
                'reports' => [
                    'links' => [
                        'self' => route('users.relationships.reports', ['user' => $this->id]),
                        'related' => route('users.reports', ['user' => $this->id]),
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

    public function included($request)
    {
        /** @phpstan-ignore-next-line */
        return collect($this->relations())
            ->filter(function ($resource) {
                return $resource->collection !== null;
            })->flatMap->toArray($request);
    }

    public function with($request)
    {
        $with = [];
        if ($this->included($request)->isNotEmpty()) {
            $with['included'] = $this->included($request);
        }
        return $with;
    }
}
