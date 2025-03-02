<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Constants\AuthConstants;
use App\DTO\Api\LoginDTO;
use App\DTO\Api\RegisterDTO;
use App\Exceptions\UserRegistrationException;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Resources\Api\UserResource;
use App\Services\Api\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PassportAuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function register(StoreUserRequest $request): JsonResponse
    {
        try {
            $dto   = RegisterDTO::fromRequest($request);
            $user  = $this->authService->register($dto);
            $token = $this->authService->createUserToken($user);

            return $this->sendResponse(
                token: $token,
                result: new UserResource($user),
                message: AuthConstants::MESSAGES['REGISTER_SUCCESS']
            );
        } catch (UserRegistrationException $e) {
            return $this->sendError(
                error: $e->getMessage(),
                code: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = LoginDTO::fromRequest($request);

            if (Auth::attempt($credentials->toArray())) {
                $user  = Auth::user();
                $token = $this->authService->createUserToken($user);

                return $this->sendResponse(
                    token: $token,
                    result: new UserResource($user),
                    message: AuthConstants::MESSAGES['LOGIN_SUCCESS']
                );
            }

            return $this->sendError(
                error: AuthConstants::MESSAGES['INVALID_CREDENTIALS'],
                code: Response::HTTP_UNAUTHORIZED
            );
        } catch (AuthenticationException $e) {
            return $this->sendError(
                error: $e->getMessage(),
                code: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->user()->token();
            $token->revoke();

            return $this->sendResponse(
                result: null,
                message: AuthConstants::MESSAGES['LOGOUT_SUCCESS']
            );
        } catch (\Throwable $th) {
            return $this->sendError(
                error: AuthConstants::MESSAGES['LOGOUT_FAILED'],
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function profile(): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            return $this->sendResponse(
                token: null,
                result: new UserResource($user),
                message: AuthConstants::MESSAGES['PROFILE_FOUND']
            );
        }

        return $this->sendError(
            error: AuthConstants::MESSAGES['PROFILE_FAILED'],
            code: Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
