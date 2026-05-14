<?php

namespace App\Modules\v0\Auth\Services\PlatformManager;

use App\Modules\BaseService;
use App\Modules\v0\Users\Models\User;
use App\Modules\v0\Users\Repositories\UserRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlatformManagerAuthService extends BaseService
{
    use ApiResponse;

    public function __construct(
        protected UserRepository $userRepository,
        protected \App\Modules\v0\Emails\Services\EmailService $emailService
    ) {
    }

    public function login(array $credentials): JsonResponse
    {
        $identifier = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (empty($identifier) || empty($password)) {
            return self::error('Email and password are required', code: 400);
        }

        $user = $this->userRepository->findByEmailIgnoreCase($identifier)
            ?? $this->userRepository->findByPhoneNumber($identifier);

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

    public function logout(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return self::success(['message' => 'Logged out successfully']);
    }



    public function requestPasswordReset(string $email): JsonResponse
    {
        $user = $this->userRepository->findByEmailIgnoreCase($email);

        if (!$user) {
            return self::error('User not found', code: 404);
        }

        $token = Str::random(64);
        $expiresAt = now()->addMinutes(60);

        DB::table('platform_manager_password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'expires_at' => $expiresAt,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $resetUrl = config('app.url') . '/auth/platform-managers/password/reset?token=' . $token . '&email=' . urlencode($user->email);

        $this->emailService->sendPasswordResetMail($user->email, $token, $resetUrl, 'Platform Manager');

        return self::success([
            'message' => 'Password reset token generated',
            'email' => $user->email,
            'reset_token' => $token,
            'reset_url' => $resetUrl,
            'expires_at' => $expiresAt,
        ]);
    }

    public function resetPassword(array $data): JsonResponse
    {
        $email = $data['email'] ?? null;
        $token = $data['token'] ?? null;
        $password = $data['password'] ?? null;
        $confirmation = $data['password_confirmation'] ?? null;

        if (empty($email) || empty($token) || empty($password) || empty($confirmation)) {
            return self::error('Email, token, password, and password confirmation are required', code: 400);
        }

        if ($password !== $confirmation) {
            return self::error('Password and confirmation must match', code: 422);
        }

        $user = $this->userRepository->findByEmailIgnoreCase($email);
        if (!$user) {
            return self::error('User not found', code: 404);
        }

        $record = DB::table('platform_manager_password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();

        if (!$record) {
            return self::error('Reset token not found or expired', code: 404);
        }

        if (!empty($record->expires_at) && now()->greaterThan(Carbon::parse($record->expires_at))) {
            DB::table('platform_manager_password_reset_tokens')->where('email', $record->email)->delete();
            return self::error('Reset token expired', code: 422);
        }

        if (!Hash::check($token, $record->token)) {
            return self::error('Invalid reset token', code: 422);
        }

        $user->password = $password;
        $user->save();

        DB::table('platform_manager_password_reset_tokens')->where('email', $record->email)->delete();

        return self::success(['message' => 'Password reset successfully']);
    }

    public function changePassword(array $data): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return self::error('Unauthorized', code: 401);
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            return self::error('Current password does not match', code: 422);
        }

        $user->password = $data['password'];
        $user->save();

        return self::success(['message' => 'Password changed successfully']);
    }

    protected function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'active' => $user->active,
            'role' => $user->role,
        ];
    }
}
