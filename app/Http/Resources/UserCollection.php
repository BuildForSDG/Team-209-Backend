<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;

class UserCollection extends ResourceCollection
{
    public $collects = UserResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "data" => $this->collection,
            'included' => $this->mergeIncludedRelations($request),
        ];
    }

    private function mergeIncludedRelations($request)
    {
        $includes = $this->collection->flatMap->included($request)->unique()->values();
        return $includes->isNotEmpty() ? $includes : new MissingValue();
    }
}
