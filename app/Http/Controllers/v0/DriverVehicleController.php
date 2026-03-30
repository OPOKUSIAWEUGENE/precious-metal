<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Fleet\StoreDriverVehicleRequest;
use App\Modules\v0\Fleets\Services\DriverVehicleService;
use Illuminate\Http\JsonResponse;

class DriverVehicleController extends Controller
{
    protected DriverVehicleService $service;

    public function __construct(DriverVehicleService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $profileId = request()->query('profile_id');
        return $this->service->all($profileId);
    }

    public function store(StoreDriverVehicleRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    public function byProfile(string $profileId): JsonResponse
    {
        return $this->service->findByProfileId($profileId);
    }
}