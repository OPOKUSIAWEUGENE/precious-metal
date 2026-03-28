<?php

namespace App\Modules\v0\Finances\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Finances\Interfaces\CommissionInterface;
use App\Modules\v0\Finances\Models\Commission;

class CommissionRepository extends BaseRepository implements CommissionInterface
{
    public function __construct()
    {
        parent::__construct(new Commission());
    }

    public function all()
    {
        return $this->model->with(['trip', 'operator', 'driverProfile'])->get();
    }

    public function find(string $id)
    {
        return $this->model->with(['trip', 'operator', 'driverProfile'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $commission = $this->find($id);
        if ($commission) {
            $commission->update($data);
            return $commission;
        }
        return null;
    }

    public function delete(string $id)
    {
        $commission = $this->find($id);
        if ($commission) {
            return $commission->delete();
        }
        return false;
    }

    public function findByTrip(string $tripId)
    {
        return $this->model->where('trip_id', $tripId)->get();
    }

    public function findByOperator(string $operatorId)
    {
        return $this->model->where('operator_id', $operatorId)->get();
    }

    public function findByDriverProfile(string $driverProfileId)
    {
        return $this->model->where('driver_profile_id', $driverProfileId)->get();
    }

    public function findByStatus($status)
    {
        return $this->model->where('status', $status)->get();
    }

    public function findByPeriod(string $periodStart, string $periodEnd)
    {
        return $this->model->whereBetween('period_start', [$periodStart, $periodEnd])
            ->orWhereBetween('period_end', [$periodStart, $periodEnd])
            ->get();
    }

    public function sumByOperator(string $operatorId, string $periodStart, string $periodEnd)
    {
        return $this->model->where('operator_id', $operatorId)
            ->whereBetween('period_start', [$periodStart, $periodEnd])
            ->sum('net_amount');
    }

    public function sumByDriverProfile(string $driverProfileId, string $periodStart, string $periodEnd)
    {
        return $this->model->where('driver_profile_id', $driverProfileId)
            ->whereBetween('period_start', [$periodStart, $periodEnd])
            ->sum('net_amount');
    }
}