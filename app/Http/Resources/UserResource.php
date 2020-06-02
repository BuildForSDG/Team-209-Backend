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
            ]
        ];
    }
}
