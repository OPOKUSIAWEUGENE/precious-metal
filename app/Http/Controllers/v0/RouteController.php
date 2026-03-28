<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Trips\StoreRouteRequest;
use App\Http\Requests\v0\Trips\UpdateRouteRequest;
use App\Modules\v0\Trips\Services\RouteService;
use Illuminate\Http\JsonResponse;

class RouteController extends Controller
{
    protected RouteService $service;

    public function __construct(RouteService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    public function store(StoreRouteRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateRouteRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }
}