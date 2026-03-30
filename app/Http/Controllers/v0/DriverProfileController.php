<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Fleet\StoreDriverProfileRequest;
use App\Http\Requests\v0\Fleet\UpdateDriverProfileRequest;
use App\Modules\v0\Fleets\Services\DriverProfileService;
use Illuminate\Http\JsonResponse;

class DriverProfileController extends Controller
{
    protected DriverProfileService $service;

    public function __construct(DriverProfileService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $operatorId = request()->query('operator_id');
        return $this->service->listDrivers($operatorId);
    }

    public function store(StoreDriverProfileRequest $request): JsonResponse
    {
        return $this->service->createDriver($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->getDriver($id);
    }

    public function update(UpdateDriverProfileRequest $request, string $id): JsonResponse
    {
        return $this->service->updateDriver($id, $request->validated());
    }
}