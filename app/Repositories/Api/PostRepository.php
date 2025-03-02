<?php

declare(strict_types = 1);

namespace App\Repositories\Api;

use App\DTO\Api\PostFilterDTO;
use App\Contracts\Repository\Api\PostRepositoryInterface;
use App\Models\Api\Post;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PostRepository implements PostRepositoryInterface
{
    public function getAll(Request $request)
    {
        $perPage = $request->get('per_page', 5);

        try {
            return Post::with('author')->paginate($perPage);
        } catch (Exception $ex) {
            return false;
        }
    }

    public function getFilteredPosts(PostFilterDTO $filterDTO)
    {
        $query = Post::with('author');

        if ($filterDTO->tag) {
            $query->where('tags', 'like', '%' . $filterDTO->tag . '%');
        }

        if ($filterDTO->query) {
            $query->where(function ($q) use ($filterDTO) {
                $q->where('title', 'like', '%' . $filterDTO->query . '%')
                    ->orWhere('content', 'like', '%' . $filterDTO->query . '%');
            });
        }

        return $query->paginate();
    }

    public function create(array $data)
    {
        try {
            $user = Post::create($data);

            return $user;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function update(array $data, $id)
    {
        try {
            $user = Post::findOrFail($id);
            $user->update($data);

            return $user;
        } catch (ModelNotFoundException $ex) {
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $user = Post::findOrFail($id);
            $user->delete();

            return response()->noContent();
        } catch (ModelNotFoundException $ex) {
            return false;
        }
    }

    public function findById($id)
    {
        try {
            $user = Post::findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return false;
        }

        return $user;
    }
}
