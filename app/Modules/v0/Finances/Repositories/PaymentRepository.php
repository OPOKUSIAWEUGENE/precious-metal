<?php

namespace App\Modules\v0\Finances\Repositories;

use App\Modules\v0\Finances\Models\Payment;
use App\Modules\v0\Finances\Interfaces\PaymentInterface;
use App\Modules\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository extends BaseRepository implements PaymentInterface
{
    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    public function find(string $id): ?Payment
    {
        return $this->model->find($id);
    }

    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): Payment
    {
        $payment = $this->find($id);
        if (!$payment) {
            throw new \Exception('Payment not found');
        }
        $payment->update($data);
        return $payment->fresh();
    }

    public function delete(string $id): bool
    {
        $payment = $this->find($id);
        if (!$payment) {
            throw new \Exception('Payment not found');
        }
        return $payment->delete();
    }

    public function findByBooking(string $bookingId): Collection
    {
        return $this->model->where('booking_id', $bookingId)->orderBy('created_at', 'desc')->get();
    }

    public function findByPassenger(string $passengerProfileId): Collection
    {
        return $this->model->where('passenger_profile_id', $passengerProfileId)->orderBy('created_at', 'desc')->get();
    }

    public function findByOperator(string $operatorId): Collection
    {
        return $this->model->where('operator_id', $operatorId)->orderBy('created_at', 'desc')->get();
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->orderBy('created_at', 'desc')->get();
    }

    public function findByTransactionId(string $transactionId): ?Payment
    {
        return $this->model->where('transaction_id', $transactionId)->first();
    }

    public function findByPeriod(string $periodStart, string $periodEnd): Collection
    {
        return $this->model->whereBetween('created_at', [$periodStart, $periodEnd])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function sumByOperator(string $operatorId, string $periodStart, string $periodEnd): float
    {
        return (float) $this->model->where('operator_id', $operatorId)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->where('status', 'COMPLETED')
            ->sum('amount');
    }

    public function sumByPassenger(string $passengerProfileId, string $periodStart, string $periodEnd): float
    {
        return (float) $this->model->where('passenger_profile_id', $passengerProfileId)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->where('status', 'COMPLETED')
            ->sum('amount');
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }
}