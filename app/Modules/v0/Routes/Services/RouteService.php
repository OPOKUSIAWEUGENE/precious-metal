<?php

namespace App\Modules\v0\Routes\Services;

use App\Modules\BaseService;
use App\Modules\v0\Routes\Repositories\RouteRepository;
use App\Modules\v0\Routes\Repositories\RouteStopRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\Routes\Resources\RouteResource;

class RouteService extends BaseService
{
    use ApiResponse;

    protected RouteRepository $repository;
    protected RouteStopRepository $stopRepository;

    public function __construct(RouteRepository $repository, RouteStopRepository $stopRepository)
    {
        $this->repository = $repository;
        $this->stopRepository = $stopRepository;
    }

    public function all(): JsonResponse
    {
        $routes = $this->repository->all();
        return self::success(RouteResource::collection($routes));
    }

    public function find(string $id): JsonResponse
    {
        $route = $this->repository->find($id);
        if (!$route) {
            return self::error('Route not found', code: 404);
        }
        return self::success(new RouteResource($route));
    }

    public function create(array $data): JsonResponse
    {
        // Validate stops exist
        if (isset($data['start_stop_id'])) {
            $startStop = $this->stopRepository->find($data['start_stop_id']);
            if (!$startStop) {
                return self::error('Start stop not found', code: 404);
            }
        }

        if (isset($data['end_stop_id'])) {
            $endStop = $this->stopRepository->find($data['end_stop_id']);
            if (!$endStop) {
                return self::error('End stop not found', code: 404);
            }
        }

        $route = $this->repository->create($data);
        return self::success(new RouteResource($route), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        // Validate stops if provided
        if (isset($data['start_stop_id'])) {
            $startStop = $this->stopRepository->find($data['start_stop_id']);
            if (!$startStop) {
                return self::error('Start stop not found', code: 404);
            }
        }

        if (isset($data['end_stop_id'])) {
            $endStop = $this->stopRepository->find($data['end_stop_id']);
            if (!$endStop) {
                return self::error('End stop not found', code: 404);
            }
        }

        $route = $this->repository->update($id, $data);
        if (!$route) {
            return self::error('Route not found', code: 404);
        }
        return self::success(new RouteResource($route), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Route not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Route deleted successfully']);
    }

    public function findByStatus(string $status): JsonResponse
    {
        $routes = $this->repository->findByStatus($status);
        return self::success(RouteResource::collection($routes));
    }
}