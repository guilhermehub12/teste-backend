<?php

declare(strict_types = 1);

namespace App\Contracts\Repository\Api;

use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function getAll(Request $request);

    public function create(array $data);

    public function update(array $data, $id);

    public function destroy($id);

    public function findById($id);
}
