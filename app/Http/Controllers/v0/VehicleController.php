<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Fleet\StoreVehicleRequest;
use App\Http\Requests\v0\Fleet\UpdateVehicleRequest;
use App\Modules\v0\Fleet\Services\VehicleService;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{
    protected VehicleService $service;

    public function __construct(VehicleService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $operatorId = request()->query('operator_id');
        return $this->service->all($operatorId);
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateVehicleRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    public function byFleetIdentifier(string $fleetIdentifier): JsonResponse
    {
        return $this->service->findByFleetIdentifier($fleetIdentifier);
    }
}