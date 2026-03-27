<?php

namespace App\Modules\v0\User\Services;

use App\Modules\BaseService;
use App\Modules\v0\User\Repositories\UserRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\User\Resources\UserResource;

class UserService extends BaseService
{
    use ApiResponse;

    protected UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): JsonResponse
    {
        $users = $this->repository->all();
        return self::success(UserResource::collection($users));
    }

    public function find(string $id): JsonResponse
    {
        $user = $this->repository->find($id);
        if (!$user) {
            return self::error('User not found', code: 404);
        }
        return self::success(new UserResource($user));
    }

    public function create(array $data): JsonResponse
    {
        $user = $this->repository->create($data);
        return self::success(new UserResource($user), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $user = $this->repository->update($id, $data);
        if (!$user) {
            return self::error('User not found', code: 404);
        }
        return self::success(new UserResource($user), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('User not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'User deleted successfully']);
    }

    public function findByEmail(string $email): JsonResponse
    {
        $user = $this->repository->findByEmail($email);
        if (!$user) {
            return self::error('User not found', code: 404);
        }
        return self::success(new UserResource($user));
    }
}