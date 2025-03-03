<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Api RESTful com Laravel 12",
 *     version="1.0.0",
 *     description="Essa é uma API REST feita com Laravel 12, Passport para autenticação e para autorização.",
 *     @OA\Contact(
 *         email="contato@delmirosolucoes.com.br"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Gerenciamento de autenticação de usuários",
 * )
 * @OA\Tag(
 *     name="Usuários",
 *     description="Gerenciamento de usuários"
 * )
 * @OA\Tag(
 *     name="Posts",
 *     description="Gerenciamento de posts"
 * )
 * 
 *  @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 * 
 */
class Controller extends BaseController
{
    use ApiResponse;
}
