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
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Usuários",
 *     description="Gerenciamento de usuários"
 * )
 */
class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Lista todos os usuários",
     *     description="Retorna uma lista paginada de todos os usuários cadastrados",
     *     operationId="listUsers",
     *     tags={"Usuários"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página para paginação",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Quantidade de registros por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de usuários retornada com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *                     @OA\Property(property="telefone", type="string", example="85999999999"),
     *                     @OA\Property(property="is_admin", type="boolean", example=false),
     *                     @OA\Property(property="is_valid", type="boolean", example=true),
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Não autorizado")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->getAll($request);

        return $this->sendResponse(
            result: UserResource::collection($users),
            message: AuthConstants::MESSAGES['LIST_SUCCESS'],
            token: null,
        );
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Criar novo usuário",
     *     description="Cria um novo usuário",
     *     operationId="storeUser",
     *     tags={"Usuários"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","telefone"},
     *             @OA\Property(property="name", type="string", example="João Silva", description="Nome completo do usuário"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com", description="Email do usuário"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123", description="Senha do usuário"),
     *             @OA\Property(property="telefone", type="string", example="85999999999", description="Telefone do usuário"),
     *             @OA\Property(property="is_valid", type="boolean", example=true, description="Status da conta"),
     *             @OA\Property(property="is_admin", type="boolean", example=false, description="Privilégios administrativos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário registrado com sucesso"),
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
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao criar usuário"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->all();
        $user = $this->userService->create($data);

        return $this->sendResponse(
            result: new UserResource($user),
            message: AuthConstants::MESSAGES['REGISTER_SUCCESS'],
            token: null,
        );
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Exibir usuário específico",
     *     description="Retorna os dados de um usuário específico",
     *     operationId="showUser",
     *     tags={"Usuários"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário encontrado"),
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
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
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
            result: new UserResource($user),
            message: AuthConstants::MESSAGES['USER_FOUND'],
            token: null,
        );
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Atualizar usuário",
     *     description="Atualiza os dados de um usuário específico",
     *     operationId="updateUser",
     *     tags={"Usuários"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="novasenha123"),
     *             @OA\Property(property="telefone", type="string", example="85999999999"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="is_admin", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário atualizado com sucesso"),
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
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->update($request->validated(), $id);

        if ($user !== false) {
            return $this->sendResponse(
                result: new UserResource($user),
                message: AuthConstants::MESSAGES['USER_UPDATED'],
                token: null,
            );
        }

        return $this->sendError(
            error: AuthConstants::MESSAGES['USER_UPDATED_FAILED'],
            code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Remover usuário",
     *     description="Remove um usuário",
     *     operationId="deleteUser",
     *     tags={"Usuários"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário removido com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $user = $this->userService->destroy($id);

        if ($user !== false) {
            return $this->sendResponse(
                result: [],
                message: AuthConstants::MESSAGES['USER_DELETED'],
                token: null,
            );
        }

        return $this->sendError(
            error: AuthConstants::MESSAGES['USER_NOT_FOUND'],
            code: Response::HTTP_NOT_FOUND
        );
    }
}
