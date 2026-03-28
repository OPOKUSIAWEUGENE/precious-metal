<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Finances\StoreSettlementRequest;
use App\Http\Requests\v0\Finances\UpdateSettlementRequest;
use App\Http\Requests\v0\Finances\StoreCommissionRequest;
use App\Http\Requests\v0\Finances\UpdateCommissionRequest;
use App\Modules\v0\Finances\Services\SettlementService;
use App\Modules\v0\Finances\Services\CommissionService;
use Illuminate\Http\JsonResponse;

class FinanceController extends Controller
{
    protected SettlementService $settlementService;
    protected CommissionService $commissionService;

    public function __construct(SettlementService $settlementService, CommissionService $commissionService)
    {
        $this->settlementService = $settlementService;
        $this->commissionService = $commissionService;
    }

    // ================== Settlement Endpoints ==================

    public function settlementsIndex(): JsonResponse
    {
        return $this->settlementService->all();
    }

    public function settlementsStore(StoreSettlementRequest $request): JsonResponse
    {
        return $this->settlementService->create($request->validated());
    }

    public function settlementsShow(string $id): JsonResponse
    {
        return $this->settlementService->find($id);
    }

    public function settlementsUpdate(UpdateSettlementRequest $request, string $id): JsonResponse
    {
        return $this->settlementService->update($id, $request->validated());
    }

    public function settlementsDestroy(string $id): JsonResponse
    {
        return $this->settlementService->delete($id);
    }

    public function settlementsByOperator(string $operatorId): JsonResponse
    {
        return $this->settlementService->findByOperator($operatorId);
    }

    public function settlementsByDriver(string $driverProfileId): JsonResponse
    {
        return $this->settlementService->findByDriverProfile($driverProfileId);
    }

    public function settlementsByStatus(string $status): JsonResponse
    {
        return $this->settlementService->findByStatus($status);
    }

    public function settlementsByPeriod(string $periodStart, string $periodEnd): JsonResponse
    {
        return $this->settlementService->findByPeriod($periodStart, $periodEnd);
    }

    // ================== Commission Endpoints ==================

    public function commissionsIndex(): JsonResponse
    {
        return $this->commissionService->all();
    }

    public function commissionsStore(StoreCommissionRequest $request): JsonResponse
    {
        return $this->commissionService->create($request->validated());
    }

    public function commissionsShow(string $id): JsonResponse
    {
        return $this->commissionService->find($id);
    }

    public function commissionsUpdate(UpdateCommissionRequest $request, string $id): JsonResponse
    {
        return $this->commissionService->update($id, $request->validated());
    }

    public function commissionsDestroy(string $id): JsonResponse
    {
        return $this->commissionService->delete($id);
    }

    public function commissionsByTrip(string $tripId): JsonResponse
    {
        return $this->commissionService->findByTrip($tripId);
    }

    public function commissionsByOperator(string $operatorId): JsonResponse
    {
        return $this->commissionService->findByOperator($operatorId);
    }

    public function commissionsByDriver(string $driverProfileId): JsonResponse
    {
        return $this->commissionService->findByDriverProfile($driverProfileId);
    }

    public function commissionsByStatus(string $status): JsonResponse
    {
        return $this->commissionService->findByStatus($status);
    }

    public function commissionsByPeriod(string $periodStart, string $periodEnd): JsonResponse
    {
        return $this->commissionService->findByPeriod($periodStart, $periodEnd);
    }

    public function commissionsSumByOperator(string $operatorId, string $periodStart, string $periodEnd): JsonResponse
    {
        return $this->commissionService->sumByOperator($operatorId, $periodStart, $periodEnd);
    }

    public function commissionsSumByDriver(string $driverProfileId, string $periodStart, string $periodEnd): JsonResponse
    {
        return $this->commissionService->sumByDriverProfile($driverProfileId, $periodStart, $periodEnd);
    }
}