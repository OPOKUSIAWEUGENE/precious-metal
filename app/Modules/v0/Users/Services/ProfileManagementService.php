<?php

namespace App\Modules\v0\Users\Services;

use App\Modules\BaseService;
use App\Modules\v0\Users\Repositories\ProfileRepository;
use App\Modules\v0\Users\Repositories\UserRepository;
use App\Modules\v0\Users\Repositories\RoleRepository;
use App\Modules\v0\Operators\Repositories\OperatorRepository;
use App\Modules\v0\Users\Models\Profile;
use App\Modules\v0\Users\Models\User;
use App\Modules\v0\Users\Enums\ProfileType;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileManagementService extends BaseService
{
    use ApiResponse;

    protected ProfileRepository $profileRepository;
    protected UserRepository $userRepository;
    protected RoleRepository $roleRepository;
    protected OperatorRepository $operatorRepository;

    public function __construct(
        ProfileRepository $profileRepository,
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        OperatorRepository $operatorRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->operatorRepository = $operatorRepository;
    }

    /**
     * List all profiles, optionally filtered by type.
     */
    public function listProfiles(?ProfileType $type): JsonResponse
    {
        try {
            if ($type === null) {
                $profiles = $this->profileRepository->all();
            } else {
                $profiles = $this->profileRepository->findByProfileType($type);
            }
            return self::success($profiles);
        } catch (\Exception $e) {
            Log::error('ProfileManagementService::listProfiles - ' . $e->getMessage());
            return self::error($e->getMessage());
        }
    }

    /**
     * Get a specific profile by ID.
     */
    public function getProfile(string $id): JsonResponse
    {
        try {
            $profile = $this->profileRepository->find($id);
            if (!$profile) {
                return self::error('Profile not found', code: 404);
            }
            return self::success($profile);
        } catch (\Exception $e) {
            Log::error('ProfileManagementService::getProfile - ' . $e->getMessage());
            return self::error($e->getMessage());
        }
    }

    /**
     * Create a new profile.
     */
    public function createProfile(array $data): JsonResponse
    {
        try {
            if (!isset($data['profile_type'])) {
                return self::error('Profile type is required', code: 400);
            }

            $allowedTypes = [ProfileType::DRIVER, ProfileType::CLIENT_OPERATOR, ProfileType::PLATFORM_OPERATOR];
            if (!in_array($data['profile_type'], $allowedTypes)) {
                return self::error('Profile type not supported', code: 400);
            }

            // Resolve target user
            $user = $this->resolveTargetUser($data['user_id'] ?? null);
            if (!$user) {
                return self::error('User not found or not authenticated', code: 401);
            }

            // Check if profile already exists for this user and type
            $existing = $this->profileRepository->findByUserIdAndProfileType($user->id, $data['profile_type']);
            if ($existing) {
                return self::error('Profile already exists for user', code: 400);
            }

            // Build and save profile
            $profileData = $this->buildProfileData($data, $user);
            $profile = $this->profileRepository->create($profileData);

            return self::success($profile, 201, 'success');
        } catch (\Exception $e) {
            Log::error('ProfileManagementService::createProfile - ' . $e->getMessage());
            return self::error($e->getMessage());
        }
    }

    /**
     * Update an existing profile.
     */
    public function updateProfile(string $id, array $data): JsonResponse
    {
        try {
            $profile = $this->profileRepository->find($id);
            if (!$profile) {
                return self::error('Profile not found', code: 404);
            }

            // Update profile type if changed
            if (isset($data['profile_type']) && $data['profile_type'] !== $profile->profileType) {
                $existing = $this->profileRepository->findByUserIdAndProfileType(
                    $profile->user->id, 
                    $data['profile_type']
                );
                if ($existing) {
                    return self::error('User already has a profile with that type', code: 400);
                }
                $profile->profile_type = $data['profile_type'];
            }

            // Update role if provided
            if (isset($data['role_id'])) {
                $role = $this->roleRepository->find($data['role_id']);
                if (!$role) {
                    throw new \Exception('Role not found');
                }
                $profile->role_id = $role->id;
            }

            // Update operator if provided
            if (isset($data['operator_id'])) {
                $operator = $this->operatorRepository->find($data['operator_id']);
                if (!$operator) {
                    throw new \Exception('Operator not found');
                }
                $profile->operator_id = $operator->id;
            }

            // Update other fields
            if (isset($data['active'])) {
                $profile->active = $data['active'];
            }
            if (isset($data['current'])) {
                $profile->current = $data['current'];
            }
            if (isset($data['license'])) {
                $profile->license = $data['license'];
            }

            $this->profileRepository->save($profile->toArray());

            return self::success($profile);
        } catch (\Exception $e) {
            Log::error('ProfileManagementService::updateProfile - ' . $e->getMessage());
            return self::error($e->getMessage());
        }
    }

    /**
     * Delete a profile.
     */
    public function deleteProfile(string $id): JsonResponse
    {
        try {
            if (!$this->profileRepository->find($id)) {
                return self::error('Profile not found', code: 404);
            }

            $this->profileRepository->delete($id);
            return self::success(['message' => 'Profile deleted successfully']);
        } catch (\Exception $e) {
            Log::error('ProfileManagementService::deleteProfile - ' . $e->getMessage());
            return self::error($e->getMessage());
        }
    }

    /**
     * Switch the current profile for a user.
     */
    public function switchCurrentProfile(string $profileId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return self::error('Unauthenticated', code: 401);
            }

            // Reload user to get profiles
            $user = $this->userRepository->find($user->id);
            if (!$user) {
                return self::error('User not found', code: 401);
            }

            $selected = $this->profileRepository->find($profileId);
            if (!$selected) {
                return self::error('Profile not found', code: 404);
            }

            // Verify profile belongs to user
            if ($selected->user_id !== $user->id) {
                return self::error('Profile does not belong to authenticated user', code: 403);
            }

            // Update all profiles
            foreach ($user->profiles as $profile) {
                $profile->current = ($profile->id === $selected->id);
                $this->profileRepository->save($profile->toArray());
            }

            return self::success($selected);
        } catch (\Exception $e) {
            Log::error('ProfileManagementService::switchCurrentProfile - ' . $e->getMessage());
            return self::error($e->getMessage());
        }
    }

    /**
     * Build profile data array.
     */
    private function buildProfileData(array $data, User $user): array
    {
        return [
            'user_id' => $user->id,
            'profile_type' => $data['profile_type'],
            'role_id' => $data['role_id'] ?? null,
            'operator_id' => $data['operator_id'] ?? null,
            'active' => $data['active'] ?? true,
            'current' => $data['current'] ?? true,
            'license' => $data['license'] ?? null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];
    }

    /**
     * Resolve the target user from request data or authenticated user.
     */
    private function resolveTargetUser(?string $userId): ?User
    {
        if ($userId) {
            return $this->userRepository->find($userId);
        }
        return Auth::user();
    }
}