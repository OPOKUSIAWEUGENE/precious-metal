<?php

namespace App\Modules\v0\Users\Services;

use App\Modules\BaseService;
use App\Modules\v0\Users\Repositories\UserRepository;
use App\Modules\v0\Users\Models\User;
use App\Modules\v0\Users\Enums\ProfileType;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PassangerAuthService extends BaseService
{
    use ApiResponse;

    protected AuthService $authService;
    protected UserRepository $userRepository;
    protected UserService $userService;
    protected GoogleOauthService $googleOauthService;
    protected GoogleAuthUrlService $googleAuthUrlService;

    public function __construct(
        AuthService $authService,
        UserRepository $userRepository,
        UserService $userService,
        GoogleOauthService $googleOauthService,
        GoogleAuthUrlService $googleAuthUrlService
    ) {
        $this->authService = $authService;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->googleOauthService = $googleOauthService;
        $this->googleAuthUrlService = $googleAuthUrlService;
    }

    /**
     * Login with username/email and password.
     */
    public function login(array $credentials): JsonResponse
    {
        // Delegate to AuthService but trim the response for passenger
        $response = $this->authService->login($credentials);
        
        $data = $response->getData();
        
        if ($data->success ?? false) {
            $userData = $data->data ?? null;
            if ($userData && isset($userData->user)) {
                $trimmed = [
                    'token' => $userData->token ?? null,
                    'user' => [
                        'id' => $userData->user->id ?? null,
                        'email' => $userData->user->email ?? null,
                        'first_name' => $userData->user->first_name ?? null,
                        'last_name' => $userData->user->last_name ?? null,
                        'phone_number' => $userData->user->phone_number ?? null,
                        'active' => $userData->user->active ?? null,
                    ],
                ];
                return self::success($trimmed);
            }
        }
        
        return $response;
    }

    /**
     * Manual registration for passengers.
     */
    public function register(array $request): JsonResponse
    {
        $phoneNumber = $request['phone_number'] ?? null;
        $password = $request['password'] ?? null;
        $confirmPassword = $request['confirm_password'] ?? null;

        // Validation
        if (empty($phoneNumber)) {
            return self::error('Phone number is required', code: 400);
        }

        if (empty($password)) {
            return self::error('Password is required', code: 400);
        }

        if (empty($confirmPassword)) {
            return self::error('Password confirmation is required', code: 400);
        }

        if ($password !== $confirmPassword) {
            return self::error('Password and confirmation must match', code: 400);
        }

        // Derive email from phone if not provided
        $email = $request['email'] ?? null;
        if (empty($email)) {
            $email = $this->resolveEmail($phoneNumber);
        }

        if (empty($email)) {
            return self::error('Unable to derive an email address', code: 400);
        }

        // Check if user already exists
        $existingUser = $this->userRepository->findByEmailIgnoreCase($email);
        if ($existingUser) {
            return self::error('User with this email already exists', code: 400);
        }

        // Create user
        $userData = [
            'email' => $email,
            'password' => $password,
            'first_name' => $request['first_name'] ?? null,
            'last_name' => $request['last_name'] ?? null,
            'phone_number' => $phoneNumber,
            'active' => true,
        ];

        $registrationResponse = $this->userService->create($userData);
        
        if (!$registrationResponse->getData()->success ?? false) {
            return $registrationResponse;
        }

        // Auto-login after registration
        return $this->login(['username' => $email, 'password' => $password, 'profile_type' => ProfileType::PASSENGER]);
    }

    /**
     * Handle Google OAuth code sign-in.
     */
    public function googleCodeSignIn(string $code): JsonResponse
    {
        if (empty($code)) {
            return self::error('Google authorization code is required', code: 400);
        }

        try {
            $profile = $this->googleOauthService->exchangeCodeForProfile($code);
            
            if (empty($profile)) {
                return self::error('Failed to exchange Google authorization code', code: 502);
            }

            return $this->processGoogleProfile($profile);
        } catch (\Exception $e) {
            Log::error('PassangerAuthService::googleCodeSignIn - ' . $e->getMessage());
            return self::error($e->getMessage(), code: 502);
        }
    }

    /**
     * Handle Google ID token sign-in.
     */
    public function googleTokenSignIn(string $idToken): JsonResponse
    {
        if (empty($idToken)) {
            return self::error('Google ID token is required', code: 400);
        }

        try {
            $profile = $this->googleOauthService->decodeIdToken($idToken);
            
            if (empty($profile)) {
                return self::error('Unable to decode Google ID token', code: 400);
            }

            return $this->processGoogleProfile($profile);
        } catch (\Exception $e) {
            Log::error('PassangerAuthService::googleTokenSignIn - ' . $e->getMessage());
            return self::error('Unable to decode Google ID token', code: 400);
        }
    }

    /**
     * Get Google authorization URL for passengers.
     */
    public function getGoogleAuthUrl(): JsonResponse
    {
        try {
            $url = $this->googleAuthUrlService->getPassengerAuthUrl();
            return self::success(['url' => $url]);
        } catch (\Exception $e) {
            Log::error('PassangerAuthService::getGoogleAuthUrl - ' . $e->getMessage());
            return self::error('Failed to generate Google auth URL');
        }
    }

    /**
     * Process Google profile and create/login user.
     */
    protected function processGoogleProfile(array $profile): JsonResponse
    {
        $email = $this->extractEmail($profile);
        
        if (empty($email)) {
            return self::error('Unable to obtain email address from Google', code: 400);
        }

        // Check if user exists
        $existingUser = $this->userRepository->findByEmailIgnoreCase($email);
        
        if ($existingUser) {
            // Update social info if needed
            $updated = $this->updateSocialInfo($existingUser, $profile);
            
            return $this->respondWithToken($existingUser, $updated);
        }

        // Create new user with Google info
        $rawPassword = Str::random(32);
        
        $userData = [
            'email' => $email,
            'password' => $rawPassword,
            'social_login_provider' => 'google',
            'social_login_id' => $profile['sub'] ?? null,
            'first_name' => $this->extractFirstName($profile),
            'last_name' => $this->extractLastName($profile),
            'phone_number' => $this->extractPhone($profile),
            'active' => true,
        ];

        $registrationResponse = $this->userService->create($userData);
        
        if (!$registrationResponse->getData()->success ?? false) {
            return $registrationResponse;
        }

        // Auto-login after registration
        return $this->login(['username' => $email, 'password' => $rawPassword, 'profile_type' => ProfileType::PASSENGER]);
    }

    /**
     * Extract email from Google profile.
     */
    protected function extractEmail(array $profile): ?string
    {
        if (isset($profile['email'])) {
            return $profile['email'];
        }
        
        if (isset($profile['email_verified']) && $profile['email_verified'] && isset($profile['sub'])) {
            return $profile['sub'];
        }
        
        return null;
    }

    /**
     * Extract phone from Google profile.
     */
    protected function extractPhone(array $profile): ?string
    {
        return $profile['phone_number'] ?? $profile['phone'] ?? null;
    }

    /**
     * Extract first name from Google profile.
     */
    protected function extractFirstName(array $profile): ?string
    {
        if (isset($profile['given_name'])) {
            return $profile['given_name'];
        }
        
        if (isset($profile['name'])) {
            $parts = explode(' ', $profile['name']);
            return $parts[0] ?? null;
        }
        
        return null;
    }

    /**
     * Extract last name from Google profile.
     */
    protected function extractLastName(array $profile): ?string
    {
        if (isset($profile['family_name'])) {
            return $profile['family_name'];
        }
        
        if (isset($profile['name'])) {
            $parts = explode(' ', $profile['name']);
            return count($parts) > 1 ? $parts[count($parts) - 1] : null;
        }
        
        return null;
    }

    /**
     * Update user social login info.
     */
    protected function updateSocialInfo(User $user, array $profile): bool
    {
        $updated = false;

        if (empty($user->social_login_provider)) {
            $user->social_login_provider = 'google';
            $updated = true;
        }

        if (empty($user->social_login_id) && isset($profile['sub'])) {
            $user->social_login_id = $profile['sub'];
            $updated = true;
        }

        if ($updated) {
            $this->userRepository->update($user->id, [
                'social_login_provider' => $user->social_login_provider,
                'social_login_id' => $user->social_login_id,
            ]);
        }

        return $updated;
    }

    /**
     * Respond with auth token.
     */
    protected function respondWithToken(User $user, bool $needsSave): JsonResponse
    {
        // Ensure passenger profile is current
        $currentUpdated = $this->userService->ensureCurrentProfile($user, ProfileType::PASSENGER);
        
        if ($needsSave || $currentUpdated) {
            $this->userRepository->update($user->id, []);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $trimmed = [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone_number' => $user->phone_number,
                'active' => $user->active,
            ],
        ];

        return self::success($trimmed);
    }

    /**
     * Resolve email from phone number.
     */
    protected function resolveEmail(string $phone): string
    {
        $phoneClean = preg_replace('/\s+/', '', $phone);
        return $phoneClean . '@passenger.rushhour.local';
    }

    /**
     * Logout.
     */
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }
}