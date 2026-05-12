<?php

namespace App\Modules\v0\Auth\Services\Customer;

use App\Modules\v0\Customers\Models\Customer;
use App\Modules\BaseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerAuthService extends BaseService
{
    use ApiResponse;

    public function __construct(protected Customer $customer)
    {
    }

    public function login(array $credentials): JsonResponse
    {
        $identifier = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;

        if (empty($identifier) || empty($password)) {
            return self::error('Username and password are required', code: 400);
        }

        $customer = $this->findByIdentifier($identifier);

        if (!$customer || !Hash::check($password, $customer->password)) {
            return self::error('Invalid credentials', code: 401);
        }

        if (!$customer->active) {
            return self::error('Account is disabled', code: 403);
        }

        $customer->update([]);
        $token = $customer->createToken('auth-token')->plainTextToken;

        return self::success([
            'token' => $token,
            'customer' => $this->customerPayload($customer),
        ]);
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

        $email = $request['email'] ?? $this->resolveEmail($phoneNumber);
        if (empty($email)) {
            return self::error('Unable to derive an email address', code: 400);
        }

        if ($this->customer->newQuery()->where('email', $email)->exists()) {
            return self::error('Customer with this email already exists', code: 400);
        }

        $customer = $this->customer->newInstance([
            'email' => $email,
            'password' => $password,
            'first_name' => $request['first_name'] ?? null,
            'last_name' => $request['last_name'] ?? null,
            'date_of_birth' => $request['date_of_birth'] ?? null,
            'nationality' => $request['nationality'] ?? null,
            'resident_address' => $request['resident_address'] ?? null,
            'selfie' => $request['selfie'] ?? null,
            'phone_number' => $phoneNumber,
            'kyc' => $request['kyc'] ?? null,
            'card_details' => $request['card_details'] ?? null,
            'active' => true,
        ]);
        $customer->save();

        return $this->login([
            'username' => $email,
            'password' => $password,
        ]);
    }

    public function logout(): JsonResponse
    {
        $customer = Auth::guard('sanctum')->user();

        if ($customer) {
            $customer->currentAccessToken()?->delete();
        }

        return self::success(['message' => 'Logged out successfully']);
    }

    public function me(): JsonResponse
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return self::error('Unauthenticated', code: 401);
        }

        return self::success([
            'id' => $customer->id,
            'email' => $customer->email,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'date_of_birth' => $customer->date_of_birth,
            'nationality' => $customer->nationality,
            'resident_address' => $customer->resident_address,
            'selfie' => $customer->selfie,
            'phone_number' => $customer->phone_number,
            'kyc' => $customer->kyc,
            'card_details' => $customer->card_details,
            'active' => $customer->active,
            'created_at' => $customer->created_at,
            'updated_at' => $customer->updated_at,
        ]);
    }

    public function requestPasswordReset(string $email): JsonResponse
    {
        $customer = $this->customer->newQuery()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

        if (!$customer) {
            return self::error('Customer not found', code: 404);
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = now()->addMinutes(60);

        DB::table('customer_password_reset_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'token' => Hash::make($token),
                'expires_at' => $expiresAt,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return self::success([
            'message' => 'Password reset token generated',
            'email' => $customer->email,
            'reset_token' => $token,
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

        $customer = $this->customer->newQuery()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
        if (!$customer) {
            return self::error('Customer not found', code: 404);
        }

        $record = DB::table('customer_password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();

        if (!$record) {
            return self::error('Reset token not found or expired', code: 404);
        }

        if (!empty($record->expires_at) && now()->greaterThan(Carbon::parse($record->expires_at))) {
            DB::table('customer_password_reset_tokens')->where('email', $record->email)->delete();
            return self::error('Reset token expired', code: 422);
        }

        if (!Hash::check($token, $record->token)) {
            return self::error('Invalid reset token', code: 422);
        }

        $customer->password = $password;
        $customer->save();

        DB::table('customer_password_reset_tokens')->where('email', $record->email)->delete();

        return self::success(['message' => 'Password reset successfully']);
    }

    protected function resolveEmail(string $phone): string
    {
        $phoneClean = preg_replace('/\s+/', '', $phone);
        return $phoneClean . '@customer.rushhour.local';
    }

    protected function findByIdentifier(string $identifier): ?Customer
    {
        $customer = $this->customer->newQuery()->whereRaw('LOWER(email) = ?', [strtolower($identifier)])->first();

        if ($customer) {
            return $customer;
        }

        return $this->customer->newQuery()->where('phone_number', $identifier)->first();
    }

    protected function customerPayload(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'email' => $customer->email,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'date_of_birth' => $customer->date_of_birth,
            'nationality' => $customer->nationality,
            'resident_address' => $customer->resident_address,
            'selfie' => $customer->selfie,
            'phone_number' => $customer->phone_number,
            'kyc' => $customer->kyc,
            'card_details' => $customer->card_details,
            'active' => $customer->active,
        ];
    }
}
