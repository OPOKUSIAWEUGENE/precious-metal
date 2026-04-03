<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\User\StoreProfileRequest;
use App\Http\Requests\v0\User\UpdateProfileRequest;
use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected ProfileService $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of all profiles.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        return $this->service->paginate((int) $perPage);
    }

    /**
     * Store a newly created profile.
     */
    public function store(StoreProfileRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    /**
     * Display the specified profile.
     */
    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    /**
     * Update the specified profile.
     */
    public function update(UpdateProfileRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Remove the specified profile.
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    /**
     * Get profiles by user ID.
     */
    public function byUser(string $userId): JsonResponse
    {
        return $this->service->findByUserId($userId);
    }

    /**
     * Get profiles by type.
     */
    public function byType(string $type): JsonResponse
    {
        try {
            $profileType = ProfileType::from(strtoupper($type));
            return $this->service->getByType($profileType);
        } catch (\ValueError $e) {
            return \App\Utils\AppResponse::error('Invalid profile type', 422);
        }
    }

    /**
     * Set a profile as current for a user.
     */
    public function setCurrent(Request $request, string $userId): JsonResponse
    {
        $request->validate([
            'profile_id' => 'required|uuid|exists:profiles,id',
        ]);

        return $this->service->setCurrent($userId, $request->profile_id);
    }
}