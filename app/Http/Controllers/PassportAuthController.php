<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Services\Api\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PassportAuthController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function register(StoreUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $this->userService->create($data);

            if (!empty($user)) {
                \Log::info('Em rota de criar Token');

                $token = $user->createToken('SoffiaAuthApp')->accessToken;

                \Log::info('User created successfully', ['user_id' => $user->id]);

                return $this->sendResponse(new UserResource($user), 'Usuário adicionado com sucesso.', $token);
            }
            \Log::error('User creation failed attempt in PassportAuthController(register)', ['data' => $data]);

            return $this->sendError('Erro ao cadastrar o usuário. Verifique os dados fornecidos.', [], 422);
        } catch (\Throwable $th) {
            \Log::error('Registration error attempt in PassportAuthController(register)', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return $this->sendError("Erro ao cadastrar o usuário. Verifique os dados fornecidos.", [], 422);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('SoffiaAuthApp')->accessToken;
            return $this->sendResponse(new UserResource(auth()->user()), 'Login realizado com sucesso.', $token);
        } else {
            return $this->sendError('Credenciais inválidas. Verifique seu email e senha.', [], 422);

        }
    }

    public function logout (Request $request) {
        try {
            $token = $request->user()->token();
            $token->revoke();
            return $this->sendResponse(null, 'Logout realizado com sucesso.', null);
        } catch (\Throwable $th) {
            \Log::error('Logout error attempt in PassportAuthController(logout)', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return $this->sendError('Erro ao realizar logout.', [], 422);
        }
    }
}
