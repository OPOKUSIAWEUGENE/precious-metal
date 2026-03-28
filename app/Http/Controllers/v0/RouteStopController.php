<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Trips\StoreRouteStopRequest;
use App\Http\Requests\v0\Trips\UpdateRouteStopRequest;
use App\Modules\v0\Trips\Services\RouteStopService;
use Illuminate\Http\JsonResponse;

class RouteStopController extends Controller
{
    protected RouteStopService $service;

    public function __construct(RouteStopService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    public function store(StoreRouteStopRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateRouteStopRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }
}