<?php

namespace App\Modules\v0\Trips\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Trips\Interfaces\TripInterface;
use App\Modules\v0\Trips\Models\Trip;

class TripRepository extends BaseRepository implements TripInterface
{
    public function __construct()
    {
        parent::__construct(new Trip());
    }

    public function all()
    {
        return $this->model->with(['route', 'vehicle', 'driver', 'departureStop', 'arrivalStop'])->get();
    }

    public function find(string $id)
    {
        return $this->model->with(['route', 'vehicle', 'driver', 'departureStop', 'arrivalStop'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $trip = $this->find($id);
        if ($trip) {
            $trip->update($data);
            return $trip;
        }
        return null;
    }

    public function delete(string $id)
    {
        $trip = $this->find($id);
        if ($trip) {
            return $trip->delete();
        }
        return false;
    }

    public function findByRouteId(string $routeId)
    {
        return $this->model->where('route_id', $routeId)->get();
    }

    public function findByVehicleId(string $vehicleId)
    {
        return $this->model->where('vehicle_id', $vehicleId)->get();
    }

    public function findByDriverProfileId(string $driverProfileId)
    {
        return $this->model->where('driver_profile_id', $driverProfileId)->get();
    }
}