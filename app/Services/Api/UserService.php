<?php

namespace App\Services\Api;

use App\Interfaces\Api\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepositoryInterface
    )
    { }

    public function getAll(Request $request)
    {
        return $this->userRepositoryInterface->getAll($request);
    }

    public function create(array $data)
    {
        return $this->userRepositoryInterface->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->userRepositoryInterface->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->userRepositoryInterface->destroy($id);
    }

    public function findById($id)
    {
        return $this->userRepositoryInterface->findById($id);
    }
}