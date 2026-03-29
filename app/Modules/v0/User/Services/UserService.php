<?php

namespace App\Modules\v0\User\Services;

use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Interfaces\UserInterface;
use App\Modules\v0\User\Models\Profile;
use App\Modules\v0\User\Models\User;
use App\Utils\AppResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected UserInterface $repository;

    public function __construct(UserInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all users.
     */
    public function all(): JsonResponse
    {
        $users = $this->repository->all();
        return AppResponse::success($users, 'Users retrieved successfully');
    }

    /**
     * Find a user by ID.
     */
    public function find(string $id): JsonResponse
    {
        $user = $this->repository->find($id);

        if (!$user) {
            return AppResponse::error('User not found', 404);
        }

        return AppResponse::success($user, 'User retrieved successfully');
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): JsonResponse
    {
        $user = $this->repository->findByEmail($email);

        if (!$user) {
            return AppResponse::error('User not found', 404);
        }

        return AppResponse::success($user, 'User retrieved successfully');
    }

    /**
     * Create a new user.
     */
    public function create(array $data): JsonResponse
    {
        // Check if email already exists
        if ($this->repository->findByEmail($data['email'])) {
            return AppResponse::error('Email already exists', 422);
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Create the user
        $user = $this->repository->create($data);

        // If a profile is provided, create it
        if (isset($data['profile'])) {
            $profileData = $data['profile'];
            $profileData['user_id'] = $user->id;
            $profileData['current'] = true;
            
            // Validate profile type
            if (isset($profileData['profile_type']) && !($profileData['profile_type'] instanceof ProfileType)) {
                $profileData['profile_type'] = ProfileType::from($profileData['profile_type']);
            }

            // Set default profile type if not provided
            if (!isset($profileData['profile_type'])) {
                $profileData['profile_type'] = ProfileType::PASSENGER;
            }

            Profile::create($profileData);
            $user = $user->fresh(['profiles.role', 'profiles.operator']);
        }

        return AppResponse::success($user, 'User created successfully', 201);
    }

    /**
     * Update a user.
     */
    public function update(string $id, array $data): JsonResponse
    {
        // Check if email is being changed and if it already exists
        if (isset($data['email'])) {
            $existingUser = $this->repository->findByEmail($data['email']);
            if ($existingUser && $existingUser->id !== $id) {
                return AppResponse::error('Email already exists', 422);
            }
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->repository->update($id, $data);

        if (!$user) {
            return AppResponse::error('User not found', 404);
        }

        return AppResponse::success($user, 'User updated successfully');
    }

    /**
     * Delete a user.
     */
    public function delete(string $id): JsonResponse
    {
        $user = $this->repository->find($id);

        if (!$user) {
            return AppResponse::error('User not found', 404);
        }

        $this->repository->delete($id);
        return AppResponse::success(null, 'User deleted successfully');
    }

    /**
     * Add a profile to a user.
     */
    public function addProfile(string $userId, array $profileData): JsonResponse
    {
        $user = $this->repository->find($userId);

        if (!$user) {
            return AppResponse::error('User not found', 404);
        }

        // Validate profile type
        if (isset($profileData['profile_type']) && !($profileData['profile_type'] instanceof ProfileType)) {
            $profileData['profile_type'] = ProfileType::from($profileData['profile_type']);
        }

        // Check if user already has a profile of this type
        if (isset($profileData['profile_type'])) {
            $existingProfile = $user->profiles()->where('profile_type', $profileData['profile_type'])->first();
            if ($existingProfile) {
                return AppResponse::error('User already has a profile of this type', 422);
            }
        }

        $user = $this->repository->addProfile($userId, $profileData);
        return AppResponse::success($user, 'Profile added successfully');
    }

    /**
     * Set current profile for a user.
     */
    public function setCurrentProfile(string $userId, string $profileId): JsonResponse
    {
        $result = $this->repository->setCurrentProfile($userId, $profileId);

        if (!$result) {
            return AppResponse::error('Failed to set profile as current. Profile may not exist or belong to user.', 422);
        }

        return AppResponse::success(null, 'Profile set as current successfully');
    }

    /**
     * Get users by profile type.
     */
    public function getByProfileType(ProfileType $type): JsonResponse
    {
        $users = $this->repository->getByProfileType($type);
        return AppResponse::success($users, 'Users retrieved successfully');
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(string $id): JsonResponse
    {
        $result = $this->repository->deactivate($id);

        if (!$result) {
            return AppResponse::error('User not found', 404);
        }

        return AppResponse::success(null, 'User deactivated successfully');
    }

    /**
     * Activate a user.
     */
    public function activate(string $id): JsonResponse
    {
        $result = $this->repository->activate($id);

        if (!$result) {
            return AppResponse::error('User not found', 404);
        }

        return AppResponse::success(null, 'User activated successfully');
    }

    /**
     * Get paginated users.
     */
    public function paginate(int $perPage = 15): JsonResponse
    {
        $users = $this->repository->paginate($perPage);
        return AppResponse::paginate($users);
    }

    /**
     * Register a user with social login.
     */
    public function registerWithSocialLogin(array $data): JsonResponse
    {
        // Check if user already exists with this social login
        $existingUser = $this->repository->findBySocialLogin(
            $data['social_login_provider'],
            $data['social_login_id']
        );

        if ($existingUser) {
            return AppResponse::success($existingUser, 'User found', 200);
        }

        // Create new user
        $userData = [
            'email' => $data['email'],
            'social_login_provider' => $data['social_login_provider'],
            'social_login_id' => $data['social_login_id'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'profile_img' => $data['profile_img'] ?? null,
            'active' => true,
        ];

        // Hash a random password for social logins
        $userData['password'] = Hash::make(bin2hex(random_bytes(16)));

        $user = $this->repository->create($userData);

        // Create default passenger profile
        Profile::create([
            'user_id' => $user->id,
            'profile_type' => ProfileType::PASSENGER,
            'current' => true,
            'active' => true,
        ]);

        $user = $user->fresh(['profiles.role', 'profiles.operator']);
        return AppResponse::success($user, 'User registered successfully', 201);
    }
}