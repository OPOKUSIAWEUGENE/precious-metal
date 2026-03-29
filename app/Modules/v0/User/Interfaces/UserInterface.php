<?php

namespace App\Modules\v0\User\Interfaces;

use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface
{
    /**
     * Get all users.
     */
    public function all(): Collection;

    /**
     * Find a user by ID.
     */
    public function find(string $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by social login.
     */
    public function findBySocialLogin(string $provider, string $socialId): ?User;

    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Update a user.
     */
    public function update(string $id, array $data): ?User;

    /**
     * Delete a user.
     */
    public function delete(string $id): bool;

    /**
     * Add a profile to a user.
     */
    public function addProfile(string $userId, array $profileData): ?User;

    /**
     * Set current profile for a user.
     */
    public function setCurrentProfile(string $userId, string $profileId): bool;

    /**
     * Get active users with pagination.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get users by profile type.
     */
    public function getByProfileType(ProfileType $type): Collection;

    /**
     * Deactivate a user.
     */
    public function deactivate(string $id): bool;

    /**
     * Activate a user.
     */
    public function activate(string $id): bool;
}