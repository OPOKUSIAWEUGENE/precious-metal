<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Modules\v0\Trips\Services\TripService;
use App\Http\Requests\v0\Trips\StoreTripRequest;
use App\Http\Requests\v0\Trips\UpdateTripRequest;
use Illuminate\Http\JsonResponse;

class TripController extends Controller
{
    protected TripService $service;

    public function __construct(TripService $service)
    {
        $this->service = $service;
    }

    /**
     * List all trips
     */
    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    /**
     * Store a new trip
     */
    public function store(StoreTripRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    /**
     * Show a single trip
     */
    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    /**
     * Update a trip
     */
    public function update(UpdateTripRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Delete a trip
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    /**
     * Get trips by route
     */
    public function byRoute(string $routeId): JsonResponse
    {
        return $this->service->findByRouteId($routeId);
    }

    /**
     * Get trips by vehicle
     */
    public function byVehicle(string $vehicleId): JsonResponse
    {
        return $this->service->findByVehicleId($vehicleId);
    }

    /**
     * Get trips by driver
     */
    public function byDriver(string $driverProfileId): JsonResponse
    {
        return $this->service->findByDriverProfileId($driverProfileId);
    }
}