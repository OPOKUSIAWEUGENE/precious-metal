<?php

namespace App\Modules\v0\Finances\Interfaces;

use App\Modules\v0\Finances\Models\Payment;
use App\Modules\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentInterface
{
    /**
     * Get all payments.
     */
    public function all(): Collection;

    /**
     * Find payment by ID.
     */
    public function find(string $id): ?Payment;

    /**
     * Create a new payment.
     */
    public function create(array $data): Payment;

    /**
     * Update a payment.
     */
    public function update(string $id, array $data): Payment;

    /**
     * Delete a payment.
     */
    public function delete(string $id): bool;

    /**
     * Find payments by booking ID.
     */
    public function findByBooking(string $bookingId): Collection;

    /**
     * Find payments by passenger profile ID.
     */
    public function findByPassenger(string $passengerProfileId): Collection;

    /**
     * Find payments by operator ID.
     */
    public function findByOperator(string $operatorId): Collection;

    /**
     * Find payments by status.
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find payments by transaction ID.
     */
    public function findByTransactionId(string $transactionId): ?Payment;

    /**
     * Find payments by period.
     */
    public function findByPeriod(string $periodStart, string $periodEnd): Collection;

    /**
     * Sum payments by operator in period.
     */
    public function sumByOperator(string $operatorId, string $periodStart, string $periodEnd): float;

    /**
     * Sum payments by passenger in period.
     */
    public function sumByPassenger(string $passengerProfileId, string $periodStart, string $periodEnd): float;

    /**
     * Paginate payments.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}