<?php

namespace App\Modules\v0\Auth\Services\General;

use App\Modules\BaseService;
use App\Modules\v0\Users\Repositories\UserRepository;
use App\Modules\v0\Users\Models\User;
use App\Modules\v0\Users\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService extends BaseService
{
    use ApiResponse;

    protected UserRepository $userRepository;
    protected UserService $userService;

    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * Login with email/phone and password.
     */
    public function login(array $credentials): JsonResponse
    {
        $identifier = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;

        if (empty($identifier) || empty($password)) {
            return self::error('Username and password are required', code: 400);
        }

        // Try to find user by email or phone number
        $user = $this->userRepository->findByEmailIgnoreCase($identifier);
        
        if (!$user) {
            $user = $this->userRepository->findByPhoneNumber($identifier);
        }

        if (!$user) {
            return self::error('Invalid credentials', code: 401);
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            return self::error('Invalid credentials', code: 401);
        }

        // Check if user is active
        if (!$user->active) {
            return self::error('Account is disabled', code: 403);
        }

        $this->userRepository->update($user->id, []);

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        $response = [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone_number' => $user->phone_number,
                'active' => $user->active,
                'current_profile' => $user->getCurrentProfile(),
                'effective_profile_type' => $user->getEffectiveProfileType(),
            ],
        ];

        return self::success($response);
    }

    /**
     * Login with email specifically (for operator login).
     */
    public function loginWithEmail(string $email, string $password): JsonResponse
    {
        if (empty($email) || empty($password)) {
            return self::error('Email and password are required', code: 400);
        }

        $user = $this->userRepository->findByEmailIgnoreCase($email);

        if (!$user) {
            return self::error('Invalid credentials', code: 401);
        }

        if (!Hash::check($password, $user->password)) {
            return self::error('Invalid credentials', code: 401);
        }

        if (!$user->active) {
            return self::error('Account is disabled', code: 403);
        }

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        $response = [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone_number' => $user->phone_number,
                'active' => $user->active,
                'current_profile' => $user->getCurrentProfile(),
                'effective_profile_type' => $user->getEffectiveProfileType(),
            ],
        ];

        return self::success($response);
    }

    /**
     * Register a new user.
     */
    public function register(array $userData): JsonResponse
    {
        // Delegate to User Service to create the User with encoded passwords etc.
        return $this->userService->create($userData);
    }

    /**
     * Logout the current user.
     */
    public function logout(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user) {
            $user->currentAccessToken()->delete();
        }
        
        return self::success(['message' => 'Logged out successfully']);
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return self::error('Unauthenticated', code: 401);
        }

        $user->load('profiles.role');
        
        return self::success([
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'social_login_provider' => $user->social_login_provider,
            'profile_img' => $user->profile_img,
            'active' => $user->active,
            'profiles' => $user->profiles,
            'current_profile' => $user->getCurrentProfile(),
            'effective_profile_type' => $user->getEffectiveProfileType(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }
}