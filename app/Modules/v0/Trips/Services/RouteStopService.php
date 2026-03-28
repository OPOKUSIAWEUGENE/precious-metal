<?php

namespace App\Modules\v0\Trips\Services;

use App\Modules\BaseService;
use App\Modules\v0\Trips\Repositories\RouteStopRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\Trips\Resources\RouteStopResource;

class RouteStopService extends BaseService
{
    use ApiResponse;

    protected RouteStopRepository $repository;

    public function __construct(RouteStopRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): JsonResponse
    {
        $stops = $this->repository->all();
        return self::success(RouteStopResource::collection($stops));
    }

    public function find(string $id): JsonResponse
    {
        $stop = $this->repository->find($id);
        if (!$stop) {
            return self::error('Route stop not found', code: 404);
        }
        return self::success(new RouteStopResource($stop));
    }

    public function create(array $data): JsonResponse
    {
        $stop = $this->repository->create($data);
        return self::success(new RouteStopResource($stop), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $stop = $this->repository->update($id, $data);
        if (!$stop) {
            return self::error('Route stop not found', code: 404);
        }
        return self::success(new RouteStopResource($stop), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Route stop not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Route stop deleted successfully']);
    }
}