<?php

namespace App\Modules\v0\Users\Services;

use App\Mail\UserModule\WelcomeMail;
use App\Modules\BaseService;
use App\Modules\v0\Emails\Services\EmailService;
use App\Modules\v0\Users\Models\User;
use App\Modules\v0\Users\Repositories\UserRepository;
use App\Modules\v0\Users\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService extends BaseService
{
    use ApiResponse;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): JsonResponse
    {
        $users = $this->repository->all();
        return self::success(UserResource::collection($users));
    }

    public function paginate(int $perPage = 15): JsonResponse
    {
        $users = $this->repository->paginate($perPage);
        return self::success(UserResource::collection($users)->response()->getData(true));
    }

    public function find(string $id): JsonResponse
    {
        $user = $this->repository->find($id);
        if (!$user) {
            return self::error('User not found', code: 404);
        }
        return self::success(new UserResource($user));
    }

    public function create(array $data): JsonResponse
    {
        $plainPassword = Str::random(10);
        $data['password'] = Hash::make($plainPassword);
        $data['change_password'] = $data['change_password'] ?? true;

        $user = DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });

        // Send welcome email with the plain password
        Mail::to($user->email)->send(new WelcomeMail($user->email, $plainPassword));

        return self::success(new UserResource($user), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        // Hash the password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['change_password'] = $data['change_password'] ?? true;
        }
        
        $user = $this->repository->update($id, $data);
        if (!$user) {
            return self::error('User not found', code: 404);
        }
        return self::success(new UserResource($user), 200);
    }

    public function requestPasswordReset(string $id): JsonResponse
    {
        $user = $this->repository->find($id);

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

        app(EmailService::class)->sendPasswordResetMail($user->email, $token, $resetUrl, 'Platform Manager');

        return self::success([
            'message' => 'Password reset token generated',
            'email' => $user->email,
            'reset_token' => $token,
            'reset_url' => $resetUrl,
            'expires_at' => $expiresAt,
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('User not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'User deleted successfully']);
    }

    public function findByEmail(string $email): JsonResponse
    {
        $user = $this->repository->findByEmail($email);
        if (!$user) {
            return self::error('User not found', code: 404);
        }
        return self::success(new UserResource($user));
    }

    /**
     * Map user to response DTO.
     */
    public function mapToResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
            'change_password' => $user->change_password,
            'active' => $user->active,
        ];
    }
}
