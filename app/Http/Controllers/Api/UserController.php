<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Constants\AuthConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;

use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\Api\UserResource;
use App\Services\Api\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->getAll($request);

        return $this->sendResponse(
            token: null,
            result: UserResource::collection($users),
            message: AuthConstants::MESSAGES['LIST_SUCCESS']
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->all();
        $user = $this->userService->create($data);

        return $this->sendResponse(
            token: null,
            result: new UserResource($user),
            message: AuthConstants::MESSAGES['REGISTER_SUCCESS']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if ($user === false) {
            return $this->sendError(
                error: AuthConstants::MESSAGES['USER_NOT_FOUND'],
                code: Response::HTTP_NOT_FOUND
            );
        }

        return $this->sendResponse(
            token: null,
            result: new UserResource($user),
            message: AuthConstants::MESSAGES['USER_FOUND']
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->update($request->validated(), $id);

        if ($user !== false) {
            return $this->sendResponse(
                token: null,
                result: new UserResource($user),
                message: AuthConstants::MESSAGES['USER_UPDATED']
            );
        }

        return $this->sendError(
            error: AuthConstants::MESSAGES['USER_UPDATED_FAILED'],
            code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = $this->userService->destroy($id);

        if ($user !== false) {
            return $this->sendResponse(
                token: null,
                result: [],
                message: AuthConstants::MESSAGES['USER_DELETED']
            );
        }

        return $this->sendError(
            error: AuthConstants::MESSAGES['USER_NOT_FOUND'],
            code: Response::HTTP_NOT_FOUND
        );
    }
}
