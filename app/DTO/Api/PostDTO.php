<?php

declare(strict_types = 1);

namespace App\DTO\Api;

use App\Http\Requests\Api\Post\StorePostRequest;

class PostDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly string $tags,
        public readonly int $user_id,
    ) {
    }

    public static function fromRequest(StorePostRequest $request): self
    {
        return new self(
            title: $request->title,
            content: $request->content,
            tags: $request->tags,
            user_id: $request->user_id
        );
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title'   => $this->title,
            'content' => $this->content,
            'tags'    => $this->tags,
            'user_id' => $this->user_id,
        ];
    }
}
