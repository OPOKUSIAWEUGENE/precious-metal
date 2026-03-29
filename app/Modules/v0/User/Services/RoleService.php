<?php

namespace App\Modules\v0\User\Services;

use App\Modules\v0\User\Interfaces\RoleInterface;
use App\Modules\v0\User\Models\Role;
use App\Utils\AppResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class RoleService
{
    protected RoleInterface $repository;

    public function __construct(RoleInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all roles.
     */
    public function all(): JsonResponse
    {
        $roles = $this->repository->all();
        return AppResponse::success($roles, 'Roles retrieved successfully');
    }

    /**
     * Find a role by ID.
     */
    public function find(string $id): JsonResponse
    {
        $role = $this->repository->find($id);

        if (!$role) {
            return AppResponse::error('Role not found', 404);
        }

        return AppResponse::success($role, 'Role retrieved successfully');
    }

    /**
     * Create a new role.
     */
    public function create(array $data): JsonResponse
    {
        // Check if role with same name already exists
        $existingRole = $this->repository->findByName($data['name']);
        if ($existingRole) {
            return AppResponse::error('Role with this name already exists', 422);
        }

        $role = $this->repository->create($data);
        return AppResponse::success($role, 'Role created successfully', 201);
    }

    /**
     * Update a role.
     */
    public function update(string $id, array $data): JsonResponse
    {
        // Check if role with same name already exists (excluding current role)
        if (isset($data['name'])) {
            $existingRole = $this->repository->findByName($data['name']);
            if ($existingRole && $existingRole->id !== $id) {
                return AppResponse::error('Role with this name already exists', 422);
            }
        }

        $role = $this->repository->update($id, $data);

        if (!$role) {
            return AppResponse::error('Role not found', 404);
        }

        return AppResponse::success($role, 'Role updated successfully');
    }

    /**
     * Delete a role.
     */
    public function delete(string $id): JsonResponse
    {
        $role = $this->repository->find($id);

        if (!$role) {
            return AppResponse::error('Role not found', 404);
        }

        // Check if role is being used by any profiles
        if ($role->profiles()->count() > 0) {
            return AppResponse::error('Cannot delete role that is assigned to profiles', 422);
        }

        $this->repository->delete($id);
        return AppResponse::success(null, 'Role deleted successfully');
    }

    /**
     * Get role by name.
     */
    public function findByName(string $name): JsonResponse
    {
        $role = $this->repository->findByName($name);

        if (!$role) {
            return AppResponse::error('Role not found', 404);
        }

        return AppResponse::success($role, 'Role retrieved successfully');
    }
}