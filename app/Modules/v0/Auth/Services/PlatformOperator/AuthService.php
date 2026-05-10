<?php

namespace App\Modules\v0\Auth\Services\PlatformOperator;

use App\Modules\BaseService;
use App\Modules\v0\Users\Enums\ProfileType;
use App\Modules\v0\Users\Models\User;
use App\Modules\v0\Users\Repositories\UserRepository;
use App\Modules\v0\Users\Resources\UserResource;
use App\Modules\v0\Users\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function login(array $credentials): JsonResponse
    {
        $identifier = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;

        if (empty($identifier) || empty($password)) {
            return self::error('Username and password are required', code: 400);
        }

        $user = $this->userRepository->findByEmailIgnoreCase($identifier)
            ?? $this->userRepository->findByPhoneNumber($identifier);

        if (!$user || !Hash::check($password, $user->password)) {
            return self::error('Invalid credentials', code: 401);
        }

        if (!$user->active) {
            return self::error('Account is disabled', code: 403);
        }

        $this->userRepository->update($user->id, []);
        $token = $user->createToken('auth-token')->plainTextToken;

        return self::success([
            'token' => $token,
            'user' => $this->userPayload($user),
        ]);
    }

    public function loginWithEmail(string $email, string $password): JsonResponse
    {
        if (empty($email) || empty($password)) {
            return self::error('Email and password are required', code: 400);
        }

        $user = $this->userRepository->findByEmailIgnoreCase($email);
        if (!$user || !Hash::check($password, $user->password)) {
            return self::error('Invalid credentials', code: 401);
        }

        if (!$user->active) {
            return self::error('Account is disabled', code: 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return self::success([
            'token' => $token,
            'user' => $this->userPayload($user),
        ]);
    }

    public function register(array $userData): JsonResponse
    {
        $userData['profile_type'] = ProfileType::PLATFORM_OPERATOR->value;

        $response = $this->userService->create($userData);
        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $user = $this->userRepository->findByEmailIgnoreCase($userData['email']);
        if ($user) {
            $this->userService->ensureCurrentProfile($user, ProfileType::PLATFORM_OPERATOR);
            $user->load('profiles.role');
            return self::success(new UserResource($user), 201);
        }

        return $response;
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return self::success(['message' => 'Logged out successfully']);
    }

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

    protected function userPayload(User $user): array
    {
        $user->loadMissing('profiles.role');

        return [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'active' => $user->active,
            'current_profile' => $user->getCurrentProfile(),
            'effective_profile_type' => $user->getEffectiveProfileType(),
        ];
    }
}
