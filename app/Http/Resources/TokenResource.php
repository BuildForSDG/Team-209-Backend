<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Class TokenResource
 * @property PersonalAccessToken accessToken
 */
class TokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::find($this->accessToken->tokenable_id);
        return [
            'id'         => strval($this->accessToken->id),
            'type'       => 'tokens',
            'attributes' => [
                'device_name'   => $this->accessToken->name,
                'token'         => $this->plainTextToken,
                'abilities'     => $this->accessToken->abilities,
                'last_used_at'  => $this->accessToken->last_used_at,
                'created_at'    => $this->accessToken->created_at,
                'updated_at'    => $this->accessToken->updated_at,
            ],
            "relationships" => [
                "users" => [
                    "links" => [
                        "self"      => "",
                        "related"   => ""
                    ],
                    "data" => [
                            "id"    => strval($this->accessToken->tokenable_id),
                            "type"  => "users"
                    ]
                ]
            ],
            "included" => [
                    "id"            => strval($this->accessToken->tokenable_id),
                    "type"          => "users",
                    "attributes"    => [
                        "name"  => $user->name,
                        "email" => $user->email,
                        "image" => $user->image
                    ]
            ]
        ];
    }
}
