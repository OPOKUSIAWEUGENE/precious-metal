<?php

namespace App\Modules\v0\Auth\Services\Customer;

use App\Models\Customer;
use App\Modules\BaseService;
use App\Modules\v0\Auth\Services\PlatformOperator\AuthService;
use App\Modules\v0\Users\Enums\ProfileType;
use App\Modules\v0\Users\Models\User;
use App\Modules\v0\Users\Repositories\ProfileRepository;
use App\Modules\v0\Users\Repositories\UserRepository;
use App\Modules\v0\Users\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerAuthService extends BaseService
{
    use ApiResponse;

    protected AuthService $authService;
    protected UserRepository $userRepository;
    protected UserService $userService;
    protected ProfileRepository $profileRepository;
    protected Customer $customer;
    protected CustomerGoogleOauthService $googleOauthService;
    protected CustomerGoogleAuthUrlService $googleAuthUrlService;

    public function __construct(
        AuthService $authService,
        UserRepository $userRepository,
        UserService $userService,
        ProfileRepository $profileRepository,
        Customer $customer,
        CustomerGoogleOauthService $googleOauthService,
        CustomerGoogleAuthUrlService $googleAuthUrlService
    ) {
        $this->authService = $authService;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->profileRepository = $profileRepository;
        $this->customer = $customer;
        $this->googleOauthService = $googleOauthService;
        $this->googleAuthUrlService = $googleAuthUrlService;
    }

    public function login(array $credentials): JsonResponse
    {
        $response = $this->authService->login($credentials);

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $payload = $response->getData()->data ?? null;
        $user = $this->resolveUserFromPayload($payload);
        if ($user) {
            $this->ensureCustomerRecord($user);
            $this->userService->ensureCurrentProfile($user, ProfileType::PASSENGER);
        }

        return self::success($payload);
    }

    public function register(array $request): JsonResponse
    {
        $phoneNumber = $request['phone_number'] ?? null;
        $password = $request['password'] ?? null;
        $confirmPassword = $request['confirm_password'] ?? null;

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

        $email = $request['email'] ?? null;
        if (empty($email)) {
            $email = $this->resolveEmail($phoneNumber);
        }

        if (empty($email)) {
            return self::error('Unable to derive an email address', code: 400);
        }

        if ($this->userRepository->findByEmailIgnoreCase($email)) {
            return self::error('User with this email already exists', code: 400);
        }

        $userData = [
            'email' => $email,
            'password' => $password,
            'first_name' => $request['first_name'] ?? null,
            'last_name' => $request['last_name'] ?? null,
            'phone_number' => $phoneNumber,
            'active' => true,
        ];

        $registrationResponse = $this->userService->create($userData);
        if ($registrationResponse->getStatusCode() >= 400) {
            return $registrationResponse;
        }

        $user = $this->userRepository->findByEmailIgnoreCase($email);
        if ($user) {
            $this->ensureCustomerRecord($user);
            $this->userService->ensureCurrentProfile($user, ProfileType::PASSENGER);
        }

        return $this->login([
            'username' => $email,
            'password' => $password,
        ]);
    }

    public function googleCodeSignIn(string $code): JsonResponse
    {
        if (empty($code)) {
            return self::error('Google authorization code is required', code: 400);
        }

        try {
            $profile = $this->googleOauthService->exchangeCodeForProfile($code, true);
            if (empty($profile)) {
                return self::error('Failed to exchange Google authorization code', code: 502);
            }

            return $this->processGoogleProfile($profile);
        } catch (\Exception $e) {
            Log::error('CustomerAuthService::googleCodeSignIn - ' . $e->getMessage());
            return self::error($e->getMessage(), code: 502);
        }
    }

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
            Log::error('CustomerAuthService::googleTokenSignIn - ' . $e->getMessage());
            return self::error('Unable to decode Google ID token', code: 400);
        }
    }

    public function getGoogleAuthUrl(): JsonResponse
    {
        try {
            return self::success(['url' => $this->googleAuthUrlService->getCustomerAuthUrl()]);
        } catch (\Exception $e) {
            Log::error('CustomerAuthService::getGoogleAuthUrl - ' . $e->getMessage());
            return self::error('Failed to generate Google auth URL');
        }
    }

    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }

    protected function processGoogleProfile(array $profile): JsonResponse
    {
        $email = $this->extractEmail($profile);

        if (empty($email)) {
            return self::error('Unable to obtain email address from Google', code: 400);
        }

        $existingUser = $this->userRepository->findByEmailIgnoreCase($email);
        if ($existingUser) {
            $updated = $this->updateSocialInfo($existingUser, $profile);
            return $this->respondWithToken($existingUser, $updated);
        }

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
        if ($registrationResponse->getStatusCode() >= 400) {
            return $registrationResponse;
        }

        return $this->respondWithToken(
            $this->userRepository->findByEmailIgnoreCase($email),
            true,
            $rawPassword
        );
    }

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

    protected function extractPhone(array $profile): ?string
    {
        return $profile['phone_number'] ?? $profile['phone'] ?? null;
    }

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

    protected function respondWithToken(User $user, bool $needsSave, ?string $password = null): JsonResponse
    {
        $this->ensureCustomerRecord($user);
        $currentUpdated = $this->userService->ensureCurrentProfile($user, ProfileType::PASSENGER);

        if ($needsSave || $currentUpdated) {
            $this->userRepository->update($user->id, []);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return self::success([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone_number' => $user->phone_number,
                'active' => $user->active,
            ],
        ]);
    }

    protected function resolveEmail(string $phone): string
    {
        $phoneClean = preg_replace('/\s+/', '', $phone);
        return $phoneClean . '@customer.rushhour.local';
    }

    protected function ensureCustomerRecord(User $user): Customer
    {
        return $this->customer->firstOrCreate(
            ['user_id' => $user->id],
            ['active' => true]
        );
    }

    protected function resolveUserFromPayload(mixed $payload): ?User
    {
        $email = $payload->user->email ?? null;
        if (!$email) {
            return null;
        }

        return $this->userRepository->findByEmailIgnoreCase($email);
    }
}
