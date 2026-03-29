<?php

namespace App\Modules\v0\User\Interfaces;

use App\Modules\v0\User\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleInterface
{
    /**
     * Get all roles.
     */
    public function all(): Collection;

    /**
     * Find a role by ID.
     */
    public function find(string $id): ?Role;

    /**
     * Find a role by name.
     */
    public function findByName(string $name): ?Role;

    /**
     * Create a new role.
     */
    public function create(array $data): Role;

    /**
     * Update a role.
     */
    public function update(string $id, array $data): ?Role;

    /**
     * Delete a role.
     */
    public function delete(string $id): bool;
}