<?php

namespace App\Modules\v0\Fleets\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Fleet\Interfaces\VehicleInterface;
use App\Modules\v0\Fleet\Models\Vehicle;

class VehicleRepository extends BaseRepository implements VehicleInterface
{
    public function __construct()
    {
        parent::__construct(new Vehicle());
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find(string $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $vehicle = $this->find($id);
        if ($vehicle) {
            $vehicle->update($data);
            return $vehicle;
        }
        return null;
    }

    public function delete(string $id)
    {
        $vehicle = $this->find($id);
        if ($vehicle) {
            return $vehicle->delete();
        }
        return false;
    }

    public function findAllByOperatorId(string $operatorId)
    {
        return $this->model->where('operator_id', $operatorId)->get();
    }

    public function findByFleetIdentifier(string $fleetIdentifier)
    {
        return $this->model->where('fleet_identifier', $fleetIdentifier)->first();
    }

    public function findAllByFleetIdentifiers(array $fleetIdentifiers)
    {
        return $this->model->whereIn('fleet_identifier', $fleetIdentifiers)->get();
    }
}