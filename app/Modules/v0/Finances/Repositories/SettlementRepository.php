<?php

namespace App\Modules\v0\Finances\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Finances\Interfaces\SettlementInterface;
use App\Modules\v0\Finances\Models\Settlement;

class SettlementRepository extends BaseRepository implements SettlementInterface
{
    public function __construct()
    {
        parent::__construct(new Settlement());
    }

    public function all()
    {
        return $this->model->with(['operator', 'driverProfile'])->get();
    }

    public function find(string $id)
    {
        return $this->model->with(['operator', 'driverProfile', 'commissions'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $settlement = $this->find($id);
        if ($settlement) {
            $settlement->update($data);
            return $settlement;
        }
        return null;
    }

    public function delete(string $id)
    {
        $settlement = $this->find($id);
        if ($settlement) {
            return $settlement->delete();
        }
        return false;
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
}