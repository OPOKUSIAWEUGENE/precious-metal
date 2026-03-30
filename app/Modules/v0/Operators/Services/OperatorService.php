<?php

namespace App\Modules\v0\Operators\Services;


use App\Modules\BaseService;
use App\Modules\v0\Operators\Repositories\OperatorRepository;
use App\Traits\ApiResponse;
use App\Modules\v0\Operator\Resources\OperatorResource;
use Illuminate\Http\JsonResponse;

class OperatorService extends BaseService
{
    use ApiResponse;

    public function __construct(OperatorRepository $repository)
    {
		$this->repository = $repository; 
	}

    public function all(): JsonResponse
    {
        $operators = $this->repository->all();
        return self::success(OperatorResource::collection($operators));
    }

    public function find(string $id): JsonResponse
    {
        $operator = $this->repository->find($id);
        if (!$operator) {
            return self::error('Operator not found', code: 404);
        }
        return self::success(new OperatorResource($operator));
    }

    public function create(array $data): JsonResponse
    {
        $operator = $this->repository->create($data);
        return self::success(new OperatorResource($operator), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $operator = $this->repository->update($id, $data);
        if (!$operator) {
            return self::error('Operator not found', code: 404);
        }
        return self::success(new OperatorResource($operator), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Operator not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Operator deleted successfully']);
    }
}