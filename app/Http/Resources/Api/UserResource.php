<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'telefone' => $this->telefone,
            'is_admin' => $this->is_admin,
            'is_valid' => $this->is_valid,
        ];
    }

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
