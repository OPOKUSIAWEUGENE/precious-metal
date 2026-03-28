<?php

namespace App\Modules\v0\Finances\Services;

use App\Modules\v0\Finances\Interfaces\PaymentInterface;
use App\Modules\BaseService;
use App\Utils\AppResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class PaymentService extends BaseService
{
    protected PaymentInterface $repository;

    public function __construct(PaymentInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all payments.
     */
    public function all(): JsonResponse
    {
        try {
            $payments = $this->repository->all();
            return AppResponse::success($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Find payment by ID.
     */
    public function find(string $id): JsonResponse
    {
        try {
            $payment = $this->repository->find($id);
            if (!$payment) {
                return AppResponse::notFound('Payment not found');
            }
            return AppResponse::success($payment, 'Payment retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Create a new payment.
     */
    public function create(array $data): JsonResponse
    {
        try {
            $payment = $this->repository->create($data);
            return AppResponse::success($payment, 'Payment created successfully', 201);
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Update a payment.
     */
    public function update(string $id, array $data): JsonResponse
    {
        try {
            $payment = $this->repository->update($id, $data);
            return AppResponse::success($payment, 'Payment updated successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Delete a payment.
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $result = $this->repository->delete($id);
            return AppResponse::success(null, 'Payment deleted successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Find payments by booking ID.
     */
    public function findByBooking(string $bookingId): JsonResponse
    {
        try {
            $payments = $this->repository->findByBooking($bookingId);
            return AppResponse::success($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Find payments by passenger profile ID.
     */
    public function findByPassenger(string $passengerProfileId): JsonResponse
    {
        try {
            $payments = $this->repository->findByPassenger($passengerProfileId);
            return AppResponse::success($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Find payments by operator ID.
     */
    public function findByOperator(string $operatorId): JsonResponse
    {
        try {
            $payments = $this->repository->findByOperator($operatorId);
            return AppResponse::success($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Find payments by status.
     */
    public function findByStatus(string $status): JsonResponse
    {
        try {
            $payments = $this->repository->findByStatus($status);
            return AppResponse::success($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Find payments by transaction ID.
     */
    public function findByTransactionId(string $transactionId): JsonResponse
    {
        try {
            $payment = $this->repository->findByTransactionId($transactionId);
            if (!$payment) {
                return AppResponse::notFound('Payment not found');
            }
            return AppResponse::success($payment, 'Payment retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Find payments by period.
     */
    public function findByPeriod(string $periodStart, string $periodEnd): JsonResponse
    {
        try {
            $payments = $this->repository->findByPeriod($periodStart, $periodEnd);
            return AppResponse::success($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Sum payments by operator.
     */
    public function sumByOperator(string $operatorId, string $periodStart, string $periodEnd): JsonResponse
    {
        try {
            $sum = $this->repository->sumByOperator($operatorId, $periodStart, $periodEnd);
            return AppResponse::success(['sum' => $sum], 'Payment sum retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Sum payments by passenger.
     */
    public function sumByPassenger(string $passengerProfileId, string $periodStart, string $periodEnd): JsonResponse
    {
        try {
            $sum = $this->repository->sumByPassenger($passengerProfileId, $periodStart, $periodEnd);
            return AppResponse::success(['sum' => $sum], 'Payment sum retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }

    /**
     * Paginate payments.
     */
    public function paginate(int $perPage = 15): JsonResponse
    {
        try {
            $payments = $this->repository->paginate($perPage);
            return AppResponse::success($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage());
        }
    }
}