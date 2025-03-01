<?php

declare(strict_types = 1);

namespace App\Services\Api;

use App\Constants\AuthConstants;
use App\DTO\Api\RegisterDTO;
use App\Http\Resources\Api\UserResource;
use App\Models\Api\User;

class AuthService
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function register(RegisterDTO $dto): User
    {
        $userData = (new UserResource($dto))->toArray(request());

        return $this->userService->create($userData);
    }

    public function createUserToken(User $user): string
    {
        return $user->createToken(AuthConstants::TOKEN_NAME)->accessToken;
    }
}
