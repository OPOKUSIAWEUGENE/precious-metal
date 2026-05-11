<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\User\StoreUserRequest;
use App\Http\Requests\v0\User\UpdateUserRequest;
use App\Modules\v0\Users\Enums\ProfileType;
use App\Modules\v0\Users\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    // Listing
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);

        return $this->service->paginate((int) $perPage);
    }

    // CRUD
    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    // Lookup
    public function findByEmail(string $email): JsonResponse
    {
        return $this->service->findByEmail($email);
    }

    // Profile assignment
    public function addProfile(Request $request, string $userId): JsonResponse
    {
        // Profile-specific columns are validated here before the service persists them.
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

    // Current profile
    public function setCurrentProfile(Request $request, string $userId): JsonResponse
    {
        $request->validate([
            'profile_id' => 'required|uuid|exists:profiles,id',
        ]);

        return $this->service->setCurrentProfile($userId, $request->profile_id);
    }

    // Profile filter
    public function byProfileType(string $type): JsonResponse
    {
        try {
            $profileType = ProfileType::from(strtoupper($type));
            return $this->service->getByProfileType($profileType);
        } catch (\ValueError $e) {
            return \App\Utils\AppResponse::error('Invalid profile type', 422);
        }
    }

    // Status
    public function deactivate(string $id): JsonResponse
    {
        return $this->service->deactivate($id);
    }

    public function activate(string $id): JsonResponse
    {
        return $this->service->activate($id);
    }
}
