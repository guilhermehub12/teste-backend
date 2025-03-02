<?php

declare(strict_types=1);

namespace App\DTO\Api;

use Illuminate\Http\Request;

class PostFilterDTO
{
    public ?string $tag;
    public ?string $query;

    public function __construct(
        ?string $tag,
        ?string $query
    ) {
        $this->tag = $tag;
        $this->query = $query;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            tag: $request->query('tag'),
            query: $request->query('query')
        );
    }
}
