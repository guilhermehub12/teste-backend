<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static function collection($resource)
    {
        return tap(parent::collection($resource), function ($collection) use ($resource) {
            $collection->additional(['meta' => [
                'total' => $resource->total(),
                'per_page' => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'last_page' => $resource->lastPage(),
            ]]);
        });
    }

}
