<?php

declare(strict_types = 1);

namespace App\Interfaces\Api;

use App\DTO\Api\PostFilterDTO;
use Illuminate\Http\Request;

interface PostRepositoryInterface
{
    public function getAll(Request $request);

    public function getFilteredPosts(PostFilterDTO $filterDTO);

    public function create(array $data);

    public function update(array $data, $id);

    public function destroy($id);

    public function findById($id);
}
