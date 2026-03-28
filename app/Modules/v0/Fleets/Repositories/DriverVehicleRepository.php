<?php

namespace App\Modules\v0\Fleets\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Fleet\Interfaces\DriverVehicleInterface;
use App\Modules\v0\Fleet\Models\DriverVehicle;

class DriverVehicleRepository extends BaseRepository implements DriverVehicleInterface
{
    public function __construct()
    {
        parent::__construct(new DriverVehicle());
    }

    public function all()
    {
        return $this->model->with(['profile', 'vehicle'])->get();
    }

    public function find(string $id)
    {
        return $this->model->with(['profile', 'vehicle'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete(string $id)
    {
        $driverVehicle = $this->find($id);
        if ($driverVehicle) {
            return $driverVehicle->delete();
        }
        return false;
    }

    public function findByProfileId(string $profileId)
    {
        return $this->model->with(['profile', 'vehicle'])->where('profile_id', $profileId)->get();
    }

    public function deleteByProfileId(string $profileId)
    {
        return $this->model->where('profile_id', $profileId)->delete();
    }
}