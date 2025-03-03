<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Constants\PostConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Post\StorePostRequest;

use App\Http\Requests\Api\Post\UpdatePostRequest;
use App\Http\Resources\Api\PostResource;
use App\Services\Api\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="Gerenciamento de posts"
 * )
 */
class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="Listar posts",
     *     description="Lista todos os posts com opções de filtro",
     *     operationId="listPosts",
     *     tags={"Posts"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Filtrar posts por tag",
     *         required=false,
     *         @OA\Schema(type="string", example="Laravel")
     *     ),
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Buscar posts por título ou conteúdo",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Quantidade de posts por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exibindo todos os posts",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Exibindo todos os posts"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Título do Post"),
     *                     @OA\Property(
     *                         property="author",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nome", type="string", example="Nome do Autor"),
     *                         @OA\Property(property="telefone", type="string", example="85999999999"),
     *                         @OA\Property(property="email", type="string", example="autor@exemplo.com")
     *                     ),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->has('tag') || $request->has('query')) {
            $posts = $this->postService->getFilteredPosts($request);
        } else {
            $posts = $this->postService->getAll($request);
        }

        if ($posts === false) {
            return $this->sendError(
                error: PostConstants::MESSAGES['POST_NOT_FOUND'],
                code: Response::HTTP_NOT_FOUND
            );
        }

        return $this->sendResponse(
            token: null,
            result: PostResource::collection($posts),
            message: PostConstants::MESSAGES['POST_INDEX']
        );
    }

    /**
     * @OA\Post(
     *     path="/posts",
     *     summary="Criar novo post",
     *     description="Cria um novo post",
     *     operationId="storePost",
     *     tags={"Posts"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "user_id", "content", "tags"},
     *             @OA\Property(property="title", type="string", example="Título do Post", description="Título do post"),
     *             @OA\Property(property="user_id", type="integer", example=1, description="ID do usuário que está criando o post"),
     *             @OA\Property(property="content", type="string", example="Conteúdo detalhado do post", description="Conteúdo do post"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 description="Tags relacionadas ao post",
     *                 @OA\Items(type="string"),
     *                 example={"Laravel", "PHP", "API"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post criado com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Título do Post"),
     *                 @OA\Property(
     *                     property="author",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nome", type="string", example="Nome do Autor"),
     *                     @OA\Property(property="telefone", type="string", example="85999999999"),
     *                     @OA\Property(property="email", type="string", example="autor@exemplo.com")
     *                 ),
     *                 @OA\Property(property="content", type="string", example="Conteúdo detalhado do post"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post não criado"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->all();
        $post = $this->postService->create($data);

        if ($post === false) {
            return $this->sendError(
                error: PostConstants::MESSAGES['POST_NOT_CREATED'],
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->sendResponse(
            token: null,
            result: new PostResource($post),
            message: PostConstants::MESSAGES['POST_CREATED']
        );
    }

    /**
     * @OA\Get(
     *     path="/posts/{id}",
     *     summary="Exibir post específico",
     *     description="Retorna os dados de um post específico",
     *     operationId="showPost",
     *     tags={"Posts"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exibindo post",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post encontrado"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Título do Post"),
     *                 @OA\Property(
     *                     property="author",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nome", type="string", example="Nome do Autor"),
     *                     @OA\Property(property="telefone", type="string", example="85999999999"),
     *                     @OA\Property(property="email", type="string", example="autor@exemplo.com")
     *                 ),
     *                 @OA\Property(property="content", type="string", example="Conteúdo detalhado do post"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $post = $this->postService->findById($id);

        if ($post === false) {
            return $this->sendError(
                error: PostConstants::MESSAGES['POST_NOT_FOUND'],
                code: Response::HTTP_NOT_FOUND
            );
        }

        return $this->sendResponse(
            token: null,
            result: new PostResource($post),
            message: PostConstants::MESSAGES['POST_SHOW']
        );
    }

    /**
     * @OA\Put(
     *     path="/posts/{id}",
     *     summary="Atualizar post",
     *     description="Atualiza os dados de um determinado post",
     *     operationId="updatePost",
     *     tags={"Posts"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Novo Título do Post"),
     *             @OA\Property(property="content", type="string", example="Novo conteúdo do post"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"Laravel", "PHP", "API"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post atualizado com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Novo Título do Post"),
     *                 @OA\Property(
     *                     property="author",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nome", type="string", example="Nome do Autor"),
     *                     @OA\Property(property="telefone", type="string", example="85999999999"),
     *                     @OA\Property(property="email", type="string", example="autor@exemplo.com")
     *                 ),
     *                 @OA\Property(property="content", type="string", example="Novo conteúdo do post"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado"
     *     )
     * )
     */
    public function update(UpdatePostRequest $request, string $id): JsonResponse
    {
        $post = $this->postService->update($request->validated(), $id);

        if ($post !== false) {
            return $this->sendResponse(
                token: null,
                result: new PostResource($post),
                message: PostConstants::MESSAGES['POST_UPDATED']
            );
        }

        return $this->sendError(
            error: PostConstants::MESSAGES['POST_NOT_UPDATED'],
            code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @OA\Delete(
     *     path="/posts/{id}",
     *     summary="Remover post",
     *     description="Remove um determinado post",
     *     operationId="deletePost",
     *     tags={"Posts"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post deletado com sucesso"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post não encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $post = $this->postService->destroy($id);

        if ($post !== false) {
            return $this->sendResponse(
                token: null,
                result: [],
                message: PostConstants::MESSAGES['POST_DELETED']
            );
        }

        return $this->sendError(
            error: PostConstants::MESSAGES['POST_NOT_DELETED'],
            code: Response::HTTP_NOT_FOUND
        );
    }
}
