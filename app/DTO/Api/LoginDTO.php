<?php

declare(strict_types = 1);

namespace App\DTO\Api;

use App\Http\Requests\Api\Auth\LoginRequest;

class LoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {
    }

    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: $request->email,
            password: $request->password
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
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
