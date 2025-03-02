<?php

declare(strict_types = 1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => [
                'id' => $this->author->id,
                'nome' => $this->author->name,
                'telefone' => $this->author->telefone,
                'email' => $this->author->email,
            ],
            'content' => $this->content,
            'tags' => $this->tags,
        ];
    }

    public static function collection($resource)
    {
        return tap(parent::collection($resource), function ($collection) use ($resource) {
            $collection->additional(['meta' => [
                'total'        => $resource->total(),
                'per_page'     => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'last_page'    => $resource->lastPage()
            ]]);
        });
    }
}
