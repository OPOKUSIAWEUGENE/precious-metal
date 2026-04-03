<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\User\StoreUserRequest;
use App\Http\Requests\v0\User\UpdateUserRequest;
use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of all users.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        return $this->service->paginate((int) $perPage);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): JsonResponse
    {
        return $this->service->findByEmail($email);
    }

    /**
     * Add a profile to a user.
     */
    public function addProfile(Request $request, string $userId): JsonResponse
    {
        $request->validate([
            'profile_type' => 'required|string|in:PLATFORM_OPERATOR,CLIENT_OPERATOR,PASSENGER,DRIVER',
            'role_id' => 'nullable|uuid|exists:roles,id',
            'operator_id' => 'nullable|uuid|exists:operators,id',
            'license' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        $profileData = $request->only([
            'profile_type',
            'role_id',
            'operator_id',
            'license',
            'active',
        ]);

        return $this->service->addProfile($userId, $profileData);
    }

    /**
     * Set current profile for a user.
     */
    public function setCurrentProfile(Request $request, string $userId): JsonResponse
    {
        $request->validate([
            'profile_id' => 'required|uuid|exists:profiles,id',
        ]);

        return $this->service->setCurrentProfile($userId, $request->profile_id);
    }

    /**
     * Get users by profile type.
     */
    public function byProfileType(string $type): JsonResponse
    {
        try {
            $profileType = ProfileType::from(strtoupper($type));
            return $this->service->getByProfileType($profileType);
        } catch (\ValueError $e) {
            return \App\Utils\AppResponse::error('Invalid profile type', 422);
        }
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(string $id): JsonResponse
    {
        return $this->service->deactivate($id);
    }

    /**
     * Activate a user.
     */
    public function activate(string $id): JsonResponse
    {
        return $this->service->activate($id);
    }

    /**
     * Register user with social login.
     */
    public function registerWithSocialLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'social_login_provider' => 'required|string',
            'social_login_id' => 'required|string',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'profile_img' => 'nullable|string|url',
        ]);

        return $this->service->registerWithSocialLogin($request->validated());
    }
}