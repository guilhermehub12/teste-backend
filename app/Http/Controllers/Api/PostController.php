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

class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {
    }

    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
            message: PostConstants::MESSAGES['POSTS_FOUND']
        );
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
