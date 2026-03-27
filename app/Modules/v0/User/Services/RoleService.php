<?php

namespace App\Modules\v0\User\Services;

use App\Modules\BaseService;
use App\Modules\v0\User\Interfaces\RoleInterface;
use App\Modules\v0\User\Repositories\RoleRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\User\Resources\RoleResource;

class RoleService extends BaseService
{
    use ApiResponse;

    protected RoleRepository $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): JsonResponse
    {
        $roles = $this->repository->all();
        return self::success(RoleResource::collection($roles));
    }

    public function find(string $id): JsonResponse
    {
        $role = $this->repository->find($id);
        if (!$role) {
            return self::error('Role not found', code: 404);
        }
        return self::success(new RoleResource($role));
    }

    public function create(array $data): JsonResponse
    {
        $role = $this->repository->create($data);
        return self::success(new RoleResource($role), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $role = $this->repository->update($id, $data);
        if (!$role) {
            return self::error('Role not found', code: 404);
        }
        return self::success(new RoleResource($role), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Role not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Role deleted successfully']);
    }

    public function findByName(string $name): JsonResponse
    {
        $role = $this->repository->findByName($name);
        if (!$role) {
            return self::error('Role not found', code: 404);
        }
        return self::success(new RoleResource($role));
    }
}