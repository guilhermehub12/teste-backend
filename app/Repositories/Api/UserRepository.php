<?php

declare(strict_types = 1);

namespace App\Repositories\Api;

use App\Interfaces\Api\UserRepositoryInterface;
use App\Models\Api\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserRepository implements UserRepositoryInterface
{
    public function getAll(Request $request)
    {
        $perPage = $request->get('per_page', 5);

        try {
            return User::paginate($perPage);
        } catch (Exception $ex) {
            return false;
        }
    }

    public function create(array $data)
    {
        try {
            $user = User::create($data);

            return $user;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function update(array $data, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($data);

            return $user;
        } catch (ModelNotFoundException $ex) {
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->noContent();
        } catch (ModelNotFoundException $ex) {
            return false;
        }
    }

    public function findById($id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return false;
        }

        return $user;
    }
}
