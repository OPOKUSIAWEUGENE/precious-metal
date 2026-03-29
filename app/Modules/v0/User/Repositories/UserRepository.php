<?php

namespace App\Modules\v0\User\Repositories;

use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Interfaces\UserInterface;
use App\Modules\v0\User\Models\Profile;
use App\Modules\v0\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserInterface
{
    /**
     * Get all users.
     */
    public function all(): Collection
    {
        return User::with(['profiles.role', 'profiles.operator'])->get();
    }

    /**
     * Find a user by ID.
     */
    public function find(string $id): ?User
    {
        return User::with(['profiles.role', 'profiles.operator'])->find($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::with(['profiles.role', 'profiles.operator'])
            ->where('email', $email)
            ->first();
    }

    /**
     * Find a user by social login.
     */
    public function findBySocialLogin(string $provider, string $socialId): ?User
    {
        return User::with(['profiles.role', 'profiles.operator'])
            ->where('social_login_provider', $provider)
            ->where('social_login_id', $socialId)
            ->first();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a user.
     */
    public function update(string $id, array $data): ?User
    {
        $user = $this->find($id);
        
        if (!$user) {
            return null;
        }

        $user->update($data);
        return $user->fresh(['profiles.role', 'profiles.operator']);
    }

    /**
     * Delete a user.
     */
    public function delete(string $id): bool
    {
        $user = $this->find($id);
        
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    /**
     * Add a profile to a user.
     */
    public function addProfile(string $userId, array $profileData): ?User
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return null;
        }

        $profileData['user_id'] = $userId;
        Profile::create($profileData);

        return $user->fresh(['profiles.role', 'profiles.operator']);
    }

    /**
     * Set current profile for a user.
     */
    public function setCurrentProfile(string $userId, string $profileId): bool
    {
        // Unset all current profiles for this user
        Profile::where('user_id', $userId)->update(['current' => false]);
        
        // Set the specified profile as current
        $profile = Profile::where('id', $profileId)
            ->where('user_id', $userId)
            ->first();

        if (!$profile) {
            return false;
        }

        $profile->current = true;
        return $profile->save();
    }

    /**
     * Get active users with pagination.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::with(['profiles.role', 'profiles.operator'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get users by profile type.
     */
    public function getByProfileType(ProfileType $type): Collection
    {
        return User::with(['profiles.role', 'profiles.operator'])
            ->whereHas('profiles', function ($query) use ($type) {
                $query->where('profile_type', $type);
            })
            ->active()
            ->get();
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(string $id): bool
    {
        $user = $this->find($id);
        
        if (!$user) {
            return false;
        }

        $user->active = false;
        return $user->save();
    }

    /**
     * Activate a user.
     */
    public function activate(string $id): bool
    {
        $user = $this->find($id);
        
        if (!$user) {
            return false;
        }

        $user->active = true;
        return $user->save();
    }
}