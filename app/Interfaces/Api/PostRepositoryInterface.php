<?php

declare(strict_types = 1);

namespace App\Interfaces\Api;

use Illuminate\Http\Request;
use App\DTO\Api\PostFilterDTO;

interface PostRepositoryInterface
{
    public function getAll(Request $request);

    public function getFilteredPosts(PostFilterDTO $filterDTO);

    public function create(array $data);

    public function update(array $data, $id);

    public function destroy($id);

    public function findById($id);
}
