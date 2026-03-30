<?php

namespace App\Modules\v0\Finances\Services;

use App\Modules\BaseService;
use App\Modules\v0\Finances\Repositories\SettlementRepository;
use App\Modules\v0\Finances\Models\Settlement;
use App\Modules\v0\Finances\Enums\SettlementStatus;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SettlementService extends BaseService
{
    use ApiResponse;

    public function __construct(SettlementRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): JsonResponse
    {
        $settlements = $this->repository->all();
        return self::success($settlements);
    }

    public function find(string $id): JsonResponse
    {
        $settlement = $this->repository->find($id);
        if (!$settlement) {
            return self::error('Settlement not found', code: 404);
        }
        return self::success($settlement);
    }

    public function create(array $data): JsonResponse
    {
        $settlement = $this->repository->create($data);
        return self::success($settlement, 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $settlement = $this->repository->update($id, $data);
        if (!$settlement) {
            return self::error('Settlement not found', code: 404);
        }
        return self::success($settlement);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Settlement not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Settlement deleted successfully']);
    }

    public function findByOperator(string $operatorId): JsonResponse
    {
        $settlements = $this->repository->findByOperator($operatorId);
        return self::success($settlements);
    }

    public function findByDriverProfile(string $driverProfileId): JsonResponse
    {
        $settlements = $this->repository->findByDriverProfile($driverProfileId);
        return self::success($settlements);
    }

    public function findByStatus(string $status): JsonResponse
    {
        try {
            $statusEnum = SettlementStatus::from($status);
            $settlements = $this->repository->findByStatus($statusEnum);
            return self::success($settlements);
        } catch (\ValueError $e) {
            return self::error('Invalid status value', code: 400);
        }
    }

    public function findByPeriod(string $periodStart, string $periodEnd): JsonResponse
    {
        $settlements = $this->repository->findByPeriod($periodStart, $periodEnd);
        return self::success($settlements);
    }
}