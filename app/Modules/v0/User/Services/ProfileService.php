<?php

namespace App\Modules\v0\User\Services;

use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Interfaces\ProfileInterface;
use App\Modules\v0\User\Models\Profile;
use App\Utils\AppResponse;
use Illuminate\Http\JsonResponse;

class ProfileService
{
    protected ProfileInterface $repository;

    public function __construct(ProfileInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all profiles.
     */
    public function all(): JsonResponse
    {
        $profiles = $this->repository->all();
        return AppResponse::success($profiles, 'Profiles retrieved successfully');
    }

    /**
     * Find a profile by ID.
     */
    public function find(string $id): JsonResponse
    {
        $profile = $this->repository->find($id);

        if (!$profile) {
            return AppResponse::error('Profile not found', 404);
        }

        return AppResponse::success($profile, 'Profile retrieved successfully');
    }

    /**
     * Get profiles by user ID.
     */
    public function findByUserId(string $userId): JsonResponse
    {
        $profiles = $this->repository->findByUserId($userId);
        return AppResponse::success($profiles, 'Profiles retrieved successfully');
    }

    /**
     * Create a new profile.
     */
    public function create(array $data): JsonResponse
    {
        // Validate profile type
        if (isset($data['profile_type']) && !($data['profile_type'] instanceof ProfileType)) {
            $data['profile_type'] = ProfileType::from($data['profile_type']);
        }

        // Check if user already has a profile of this type
        if (isset($data['user_id']) && isset($data['profile_type'])) {
            $existingProfile = $this->repository->findByUserIdAndType(
                $data['user_id'],
                $data['profile_type']
            );
            if ($existingProfile) {
                return AppResponse::error('User already has a profile of this type', 422);
            }
        }

        // If this is the first profile for user, set it as current
        if (isset($data['user_id'])) {
            $userProfiles = $this->repository->findByUserId($data['user_id']);
            if ($userProfiles->isEmpty()) {
                $data['current'] = true;
            }
        }

        // Validate role exists if provided
        if (isset($data['role_id'])) {
            $role = \App\Modules\v0\User\Models\Role::find($data['role_id']);
            if (!$role) {
                return AppResponse::error('Role not found', 404);
            }
        }

        // Validate operator exists if provided
        if (isset($data['operator_id'])) {
            $operator = \App\Modules\v0\Operator\Models\Operator::find($data['operator_id']);
            if (!$operator) {
                return AppResponse::error('Operator not found', 404);
            }
        }

        $profile = $this->repository->create($data);
        return AppResponse::success($profile, 'Profile created successfully', 201);
    }

    /**
     * Update a profile.
     */
    public function update(string $id, array $data): JsonResponse
    {
        // Validate profile type if provided
        if (isset($data['profile_type']) && !($data['profile_type'] instanceof ProfileType)) {
            $data['profile_type'] = ProfileType::from($data['profile_type']);
        }

        // Validate role if provided
        if (isset($data['role_id'])) {
            $role = \App\Modules\v0\User\Models\Role::find($data['role_id']);
            if (!$role) {
                return AppResponse::error('Role not found', 404);
            }
        }

        // Validate operator if provided
        if (isset($data['operator_id'])) {
            $operator = \App\Modules\v0\Operator\Models\Operator::find($data['operator_id']);
            if (!$operator) {
                return AppResponse::error('Operator not found', 404);
            }
        }

        $profile = $this->repository->update($id, $data);

        if (!$profile) {
            return AppResponse::error('Profile not found', 404);
        }

        return AppResponse::success($profile, 'Profile updated successfully');
    }

    /**
     * Delete a profile.
     */
    public function delete(string $id): JsonResponse
    {
        $profile = $this->repository->find($id);

        if (!$profile) {
            return AppResponse::error('Profile not found', 404);
        }

        // Cannot delete the only profile for a user
        $userProfiles = $this->repository->findByUserId($profile->user_id);
        if ($userProfiles->count() === 1) {
            return AppResponse::error('Cannot delete the only profile for a user', 422);
        }

        $this->repository->delete($id);
        return AppResponse::success(null, 'Profile deleted successfully');
    }

    /**
     * Set a profile as current for a user.
     */
    public function setCurrent(string $userId, string $profileId): JsonResponse
    {
        $result = $this->repository->setCurrent($userId, $profileId);

        if (!$result) {
            return AppResponse::error('Failed to set profile as current. Profile may not exist or belong to user.', 422);
        }

        return AppResponse::success(null, 'Profile set as current successfully');
    }

    /**
     * Get profiles by type.
     */
    public function getByType(ProfileType $type): JsonResponse
    {
        $profiles = $this->repository->getByType($type);
        return AppResponse::success($profiles, 'Profiles retrieved successfully');
    }

    /**
     * Get paginated profiles.
     */
    public function paginate(int $perPage = 15): JsonResponse
    {
        $profiles = $this->repository->paginate($perPage);
        return AppResponse::paginate($profiles);
    }
}