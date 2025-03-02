<?php

declare(strict_types = 1);

namespace App\Services\Api;

use App\DTO\Api\PostFilterDTO;
use App\Contracts\Repository\Api\PostRepositoryInterface;
use Illuminate\Http\Request;

class PostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepositoryInterface
    ) {
    }

    public function getAll(Request $request)
    {
        return $this->postRepositoryInterface->getAll($request);
    }

    public function getFilteredPosts(Request $request)
    {
        $filterDTO = PostFilterDTO::fromRequest($request);

        return $this->postRepositoryInterface->getFilteredPosts($filterDTO);
    }

    public function create(array $data)
    {
        return $this->postRepositoryInterface->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->postRepositoryInterface->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->postRepositoryInterface->destroy($id);
    }

    public function findById($id)
    {
        return $this->postRepositoryInterface->findById($id);
    }
}
