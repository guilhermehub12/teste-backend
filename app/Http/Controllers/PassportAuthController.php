<?php

declare(strict_types=1);

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
use OpenApi\Annotations as OA;

class PassportAuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Registra um novo usuário",
     *     description="Endpoint para registrar um novo usuário na aplicação e retorna um token de acesso",
     *     operationId="register",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do usuário para registro",
     *         @OA\JsonContent(
     *             required={"name","email","password","telefone"},
     *             @OA\Property(property="name", type="string", example="João da Silva", description="Nome completo do usuário"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com", description="E-mail do usuário"),
     *             @OA\Property(property="password", type="string", format="password", example="senha1234@", description="Senha do usuário (min 8 caracteres)"),
     *             @OA\Property(property="telefone", type="string", example="85999999999", description="Número de telefone do usuário"),
     *             @OA\Property(property="is_valid", type="boolean", example=true, description="Status de usuário (ativo ou inativo)"),
     *             @OA\Property(property="is_admin", type="boolean", example=false, description="Permissão de administrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário registrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário registrado com sucesso."),
     *             @OA\Property(property="token", type="string", example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João da Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *                 @OA\Property(property="password", type="string", example="senha1234@"),
     *                 @OA\Property(property="telefone", type="string", example="85999999999"),
     *                 @OA\Property(property="is_valid", type="boolean", example=true),
     *                 @OA\Property(property="is_admin", type="boolean", example=false),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro ao cadastrar o usuário. Verifique os dados fornecidos.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Realiza login do usuário",
     *     description="Autentica o usuário e retorna um token de acesso",
     *     operationId="login", 
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com", description="Email do usuário"),
     *             @OA\Property(property="password", type="string", format="password", example="senha1234@", description="Senha do usuário")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login realizado com sucesso"),
     *             @OA\Property(property="token", type="string", example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *                 @OA\Property(property="telefone", type="string", example="85999999999"),
     *                 @OA\Property(property="is_valid", type="boolean", example=true),
     *                 @OA\Property(property="is_admin", type="boolean", example=false),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Realiza logout do usuário",
     *     description="Revoga o token de acesso do usuário atual",
     *     operationId="logout",
     *     tags={"Autenticação"},
     *     security={{"passport":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao realizar logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Falha ao realizar logout")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();

            return $this->sendResponse(
                token: null,
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

    /**
     * @OA\Get(
     *     path="/auth/profile",
     *     summary="Retorna o perfil do usuário",
     *     description="Obtém os dados do perfil do usuário autenticado",
     *     operationId="profile",
     *     tags={"Autenticação"},
     *     security={{"passport":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Perfil encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Perfil encontrado"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *                 @OA\Property(property="telefone", type="string", example="85999999999"),
     *                 @OA\Property(property="is_valid", type="boolean", example=true),
     *                 @OA\Property(property="is_admin", type="boolean", example=false),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao buscar perfil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Perfil não encontrado")
     *         )
     *     )
     * )
     */
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
