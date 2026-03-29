<?php

namespace App\Modules\v0\User\Interfaces;

use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Models\Profile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProfileInterface
{
    /**
     * Get all profiles.
     */
    public function all(): Collection;

    /**
     * Find a profile by ID.
     */
    public function find(string $id): ?Profile;

    /**
     * Find profiles by user ID.
     */
    public function findByUserId(string $userId): Collection;

    /**
     * Find a profile by user ID and type.
     */
    public function findByUserIdAndType(string $userId, ProfileType $type): ?Profile;

    /**
     * Create a new profile.
     */
    public function create(array $data): Profile;

    /**
     * Update a profile.
     */
    public function update(string $id, array $data): ?Profile;

    /**
     * Delete a profile.
     */
    public function delete(string $id): bool;

    /**
     * Set a profile as current for a user.
     */
    public function setCurrent(string $userId, string $profileId): bool;

    /**
     * Get active profiles with pagination.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get profiles by type.
     */
    public function getByType(ProfileType $type): Collection;
}