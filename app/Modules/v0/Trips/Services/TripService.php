<?php

namespace App\Modules\v0\Trips\Services;

use App\Modules\BaseService;
use App\Modules\v0\Trips\Repositories\TripRepository;
use App\Modules\v0\Trips\Repositories\BookingRepository;
use App\Modules\v0\Routes\Repositories\RouteRepository;
use App\Modules\v0\Routes\Repositories\RouteStopRepository;
use App\Modules\v0\Fleets\Repositories\VehicleRepository;
use App\Modules\v0\User\Repositories\ProfileRepository;
use App\Modules\v0\Trips\Enums\TripStatus;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\Trips\Resources\TripResource;

class TripService extends BaseService
{
    use ApiResponse;

    protected RouteRepository $routeRepository;
    protected RouteStopRepository $stopRepository;
    protected VehicleRepository $vehicleRepository;
    protected ProfileRepository $profileRepository;

    public function __construct(
        TripRepository $repository,
        RouteRepository $routeRepository,
        RouteStopRepository $stopRepository,
        VehicleRepository $vehicleRepository,
        ProfileRepository $profileRepository
    ) {
        $this->repository = $repository;
        $this->routeRepository = $routeRepository;
        $this->stopRepository = $stopRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->profileRepository = $profileRepository;
    }

    public function all(): JsonResponse
    {
        $trips = $this->repository->all();
        return self::success(TripResource::collection($trips));
    }

    public function find(string $id): JsonResponse
    {
        $trip = $this->repository->find($id);
        if (!$trip) {
            return self::error('Trip not found', code: 404);
        }
        return self::success(new TripResource($trip));
    }

    public function create(array $data): JsonResponse
    {
        // Validate route exists
        $route = $this->routeRepository->find($data['route_id']);
        if (!$route) {
            return self::error('Route not found', code: 404);
        }

        // Validate vehicle exists
        $vehicle = $this->vehicleRepository->find($data['vehicle_id']);
        if (!$vehicle) {
            return self::error('Vehicle not found', code: 404);
        }

        // Validate driver profile exists
        $driver = $this->profileRepository->find($data['driver_profile_id']);
        if (!$driver) {
            return self::error('Driver profile not found', code: 404);
        }

        // Validate departure stop exists
        $departureStop = $this->stopRepository->find($data['departure_stop_id']);
        if (!$departureStop) {
            return self::error('Departure stop not found', code: 404);
        }

        // Validate arrival stop exists
        $arrivalStop = $this->stopRepository->find($data['arrival_stop_id']);
        if (!$arrivalStop) {
            return self::error('Arrival stop not found', code: 404);
        }

        $tripData = [
            'route_id' => $data['route_id'],
            'vehicle_id' => $data['vehicle_id'],
            'driver_profile_id' => $data['driver_profile_id'],
            'scheduled_at' => $data['scheduled_at'],
            'departure_stop_id' => $data['departure_stop_id'],
            'arrival_stop_id' => $data['arrival_stop_id'],
            'status' => $data['status'] ?? TripStatus::PENDING_PAYMENT,
        ];

        $trip = $this->repository->create($tripData);
        return self::success(new TripResource($trip), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        // Validate route if provided
        if (isset($data['route_id'])) {
            $route = $this->routeRepository->find($data['route_id']);
            if (!$route) {
                return self::error('Route not found', code: 404);
            }
        }

        // Validate vehicle if provided
        if (isset($data['vehicle_id'])) {
            $vehicle = $this->vehicleRepository->find($data['vehicle_id']);
            if (!$vehicle) {
                return self::error('Vehicle not found', code: 404);
            }
        }

        // Validate driver if provided
        if (isset($data['driver_profile_id'])) {
            $driver = $this->profileRepository->find($data['driver_profile_id']);
            if (!$driver) {
                return self::error('Driver profile not found', code: 404);
            }
        }

        // Validate stops if provided
        if (isset($data['departure_stop_id'])) {
            $stop = $this->stopRepository->find($data['departure_stop_id']);
            if (!$stop) {
                return self::error('Departure stop not found', code: 404);
            }
        }

        if (isset($data['arrival_stop_id'])) {
            $stop = $this->stopRepository->find($data['arrival_stop_id']);
            if (!$stop) {
                return self::error('Arrival stop not found', code: 404);
            }
        }

        $trip = $this->repository->update($id, $data);
        if (!$trip) {
            return self::error('Trip not found', code: 404);
        }
        return self::success(new TripResource($trip), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Trip not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Trip deleted successfully']);
    }

    public function findByRouteId(string $routeId): JsonResponse
    {
        $trips = $this->repository->findByRouteId($routeId);
        return self::success(TripResource::collection($trips));
    }

    public function findByVehicleId(string $vehicleId): JsonResponse
    {
        $trips = $this->repository->findByVehicleId($vehicleId);
        return self::success(TripResource::collection($trips));
    }

    public function findByDriverProfileId(string $driverProfileId): JsonResponse
    {
        $trips = $this->repository->findByDriverProfileId($driverProfileId);
        return self::success(TripResource::collection($trips));
    }
}