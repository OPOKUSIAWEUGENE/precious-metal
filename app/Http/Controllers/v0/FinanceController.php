<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Finances\StoreSettlementRequest;
use App\Http\Requests\v0\Finances\UpdateSettlementRequest;
use App\Http\Requests\v0\Finances\StoreCommissionRequest;
use App\Http\Requests\v0\Finances\UpdateCommissionRequest;
use App\Http\Requests\v0\Finances\StorePaymentRequest;
use App\Http\Requests\v0\Finances\UpdatePaymentRequest;
use App\Modules\v0\Finances\Services\SettlementService;
use App\Modules\v0\Finances\Services\CommissionService;
use App\Modules\v0\Finances\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class FinanceController extends Controller
{
    protected SettlementService $settlementService;
    protected CommissionService $commissionService;
    protected PaymentService $paymentService;

    public function __construct(SettlementService $settlementService, CommissionService $commissionService, PaymentService $paymentService)
    {
        $this->settlementService = $settlementService;
        $this->commissionService = $commissionService;
        $this->paymentService = $paymentService;
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

    // ================== Payment Endpoints ==================

    public function paymentsIndex(): JsonResponse
    {
        return $this->paymentService->all();
    }

    public function paymentsStore(StorePaymentRequest $request): JsonResponse
    {
        return $this->paymentService->create($request->validated());
    }

    public function paymentsShow(string $id): JsonResponse
    {
        return $this->paymentService->find($id);
    }

    public function paymentsUpdate(UpdatePaymentRequest $request, string $id): JsonResponse
    {
        return $this->paymentService->update($id, $request->validated());
    }

    public function paymentsDestroy(string $id): JsonResponse
    {
        return $this->paymentService->delete($id);
    }

    public function paymentsByBooking(string $bookingId): JsonResponse
    {
        return $this->paymentService->findByBooking($bookingId);
    }

    public function paymentsByPassenger(string $passengerProfileId): JsonResponse
    {
        return $this->paymentService->findByPassenger($passengerProfileId);
    }

    public function paymentsByOperator(string $operatorId): JsonResponse
    {
        return $this->paymentService->findByOperator($operatorId);
    }

    public function paymentsByStatus(string $status): JsonResponse
    {
        return $this->paymentService->findByStatus($status);
    }

    public function paymentsByTransactionId(string $transactionId): JsonResponse
    {
        return $this->paymentService->findByTransactionId($transactionId);
    }

    public function paymentsByPeriod(string $periodStart, string $periodEnd): JsonResponse
    {
        return $this->paymentService->findByPeriod($periodStart, $periodEnd);
    }

    public function paymentsSumByOperator(string $operatorId, string $periodStart, string $periodEnd): JsonResponse
    {
        return $this->paymentService->sumByOperator($operatorId, $periodStart, $periodEnd);
    }

    public function paymentsSumByPassenger(string $passengerProfileId, string $periodStart, string $periodEnd): JsonResponse
    {
        return $this->paymentService->sumByPassenger($passengerProfileId, $periodStart, $periodEnd);
    }
}