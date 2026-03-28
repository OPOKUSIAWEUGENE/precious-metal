<?php

namespace App\Modules\v0\Finances\Services;

use App\Modules\BaseService;
use App\Modules\v0\Finances\Repositories\CommissionRepository;
use App\Modules\v0\Finances\Models\Commission;
use App\Modules\v0\Finances\Enums\CommissionStatus;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CommissionService extends BaseService
{
    use ApiResponse;

    protected CommissionRepository $repository;

    public function __construct(CommissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): JsonResponse
    {
        $commissions = $this->repository->all();
        return self::success($commissions);
    }

    public function find(string $id): JsonResponse
    {
        $commission = $this->repository->find($id);
        if (!$commission) {
            return self::error('Commission not found', code: 404);
        }
        return self::success($commission);
    }

    public function create(array $data): JsonResponse
    {
        $commission = $this->repository->create($data);
        return self::success($commission, 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $commission = $this->repository->update($id, $data);
        if (!$commission) {
            return self::error('Commission not found', code: 404);
        }
        return self::success($commission);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Commission not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Commission deleted successfully']);
    }

    public function findByTrip(string $tripId): JsonResponse
    {
        $commissions = $this->repository->findByTrip($tripId);
        return self::success($commissions);
    }

    public function findByOperator(string $operatorId): JsonResponse
    {
        $commissions = $this->repository->findByOperator($operatorId);
        return self::success($commissions);
    }

    public function findByDriverProfile(string $driverProfileId): JsonResponse
    {
        $commissions = $this->repository->findByDriverProfile($driverProfileId);
        return self::success($commissions);
    }

    public function findByStatus(string $status): JsonResponse
    {
        try {
            $statusEnum = CommissionStatus::from($status);
            $commissions = $this->repository->findByStatus($statusEnum);
            return self::success($commissions);
        } catch (\ValueError $e) {
            return self::error('Invalid status value', code: 400);
        }
    }

    public function findByPeriod(string $periodStart, string $periodEnd): JsonResponse
    {
        $commissions = $this->repository->findByPeriod($periodStart, $periodEnd);
        return self::success($commissions);
    }

    public function sumByOperator(string $operatorId, string $periodStart, string $periodEnd): JsonResponse
    {
        $sum = $this->repository->sumByOperator($operatorId, $periodStart, $periodEnd);
        return self::success(['total' => $sum]);
    }

    public function sumByDriverProfile(string $driverProfileId, string $periodStart, string $periodEnd): JsonResponse
    {
        $sum = $this->repository->sumByDriverProfile($driverProfileId, $periodStart, $periodEnd);
        return self::success(['total' => $sum]);
    }
}