<?php

namespace App\Modules\v0\User\Repositories;

use App\Modules\v0\User\Interfaces\RoleInterface;
use App\Modules\v0\User\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleInterface
{
    /**
     * Get all roles.
     */
    public function all(): Collection
    {
        return Role::all();
    }

    /**
     * Find a role by ID.
     */
    public function find(string $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * Find a role by name.
     */
    public function findByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    /**
     * Create a new role.
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update a role.
     */
    public function update(string $id, array $data): ?Role
    {
        $role = $this->find($id);
        
        if (!$role) {
            return null;
        }

        $role->update($data);
        return $role->fresh();
    }

    /**
     * Delete a role.
     */
    public function delete(string $id): bool
    {
        $role = $this->find($id);
        
        if (!$role) {
            return false;
        }

        return $role->delete();
    }
}