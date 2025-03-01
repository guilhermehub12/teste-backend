<?php

declare(strict_types = 1);

namespace App\DTO\Api;

use App\Http\Requests\Api\User\StoreUserRequest;

class RegisterDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $telefone,
        public readonly bool $is_admin,
        public readonly bool $is_valid
    ) {
    }

    public static function fromRequest(StoreUserRequest $request): self
    {
        return new self(
            name: $request->name,
            email: $request->email,
            password: bcrypt($request->password),
            telefone: $request->telefone,
            is_admin: $request->is_admin,
            is_valid: $request->is_valid,
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
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'telefone' => $this->telefone,
            'is_admin' => $this->is_admin,
            'is_valid' => $this->is_valid,
        ];
    }
}
