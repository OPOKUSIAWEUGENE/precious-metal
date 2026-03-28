<?php

namespace App\Modules\v0\Users\Services;

use App\Modules\BaseService;
use App\Modules\v0\Users\Repositories\ProfileRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\Users\Resources\ProfileResource;

class ProfileService extends BaseService
{
    use ApiResponse;

    protected ProfileRepository $repository;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): JsonResponse
    {
        $profiles = $this->repository->all();
        return self::success(ProfileResource::collection($profiles));
    }

    public function find(string $id): JsonResponse
    {
        $profile = $this->repository->find($id);
        if (!$profile) {
            return self::error('Profile not found', code: 404);
        }
        return self::success(new ProfileResource($profile));
    }

    public function create(array $data): JsonResponse
    {
        $profile = $this->repository->create($data);
        return self::success(new ProfileResource($profile), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $profile = $this->repository->update($id, $data);
        if (!$profile) {
            return self::error('Profile not found', code: 404);
        }
        return self::success(new ProfileResource($profile), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Profile not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Profile deleted successfully']);
    }

    public function findByUserId(string $userId): JsonResponse
    {
        $profiles = $this->repository->findByUserId($userId);
        return self::success(ProfileResource::collection($profiles));
    }

    public function getCurrentProfile(string $userId): JsonResponse
    {
        $profile = $this->repository->getCurrentProfile($userId);
        if (!$profile) {
            return self::error('Current profile not found', code: 404);
        }
        return self::success(new ProfileResource($profile));
    }
}