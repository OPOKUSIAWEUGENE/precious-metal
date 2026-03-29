<?php

namespace App\Modules\v0\User\Repositories;

use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Interfaces\ProfileInterface;
use App\Modules\v0\User\Models\Profile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileRepository implements ProfileInterface
{
    /**
     * Get all profiles.
     */
    public function all(): Collection
    {
        return Profile::with(['role', 'operator'])->get();
    }

    /**
     * Find a profile by ID.
     */
    public function find(string $id): ?Profile
    {
        return Profile::with(['role', 'operator', 'user'])->find($id);
    }

    /**
     * Find profiles by user ID.
     */
    public function findByUserId(string $userId): Collection
    {
        return Profile::with(['role', 'operator'])
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Find a profile by user ID and type.
     */
    public function findByUserIdAndType(string $userId, ProfileType $type): ?Profile
    {
        return Profile::with(['role', 'operator'])
            ->where('user_id', $userId)
            ->where('profile_type', $type)
            ->first();
    }

    /**
     * Create a new profile.
     */
    public function create(array $data): Profile
    {
        return Profile::create($data);
    }

    /**
     * Update a profile.
     */
    public function update(string $id, array $data): ?Profile
    {
        $profile = $this->find($id);
        
        if (!$profile) {
            return null;
        }

        $profile->update($data);
        return $profile->fresh(['role', 'operator']);
    }

    /**
     * Delete a profile.
     */
    public function delete(string $id): bool
    {
        $profile = $this->find($id);
        
        if (!$profile) {
            return false;
        }

        return $profile->delete();
    }

    /**
     * Set a profile as current for a user.
     */
    public function setCurrent(string $userId, string $profileId): bool
    {
        // First, unset all current profiles for this user
        Profile::where('user_id', $userId)->update(['current' => false]);
        
        // Then, set the specified profile as current
        $profile = $this->find($profileId);
        
        if (!$profile || $profile->user_id !== $userId) {
            return false;
        }

        $profile->current = true;
        return $profile->save();
    }

    /**
     * Get active profiles with pagination.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Profile::with(['role', 'operator', 'user'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get profiles by type.
     */
    public function getByType(ProfileType $type): Collection
    {
        return Profile::with(['role', 'operator', 'user'])
            ->ofType($type)
            ->active()
            ->get();
    }
}