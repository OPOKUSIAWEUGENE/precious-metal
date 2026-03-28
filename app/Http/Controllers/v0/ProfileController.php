<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\User\StoreProfileRequest;
use App\Http\Requests\v0\User\UpdateProfileRequest;
use App\Modules\v0\Users\Services\ProfileService;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    protected ProfileService $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateProfileRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    public function byUser(string $userId): JsonResponse
    {
        return $this->service->findByUserId($userId);
    }

    public function currentProfile(string $userId): JsonResponse
    {
        return $this->service->getCurrentProfile($userId);
    }
}