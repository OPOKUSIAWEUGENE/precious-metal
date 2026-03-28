<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Modules\v0\Routes\Services\RouteService;
use App\Modules\v0\Routes\Services\RouteStopService;
use App\Http\Requests\v0\Routes\StoreRouteRequest;
use App\Http\Requests\v0\Routes\UpdateRouteRequest;
use App\Http\Requests\v0\Routes\StoreRouteStopRequest;
use App\Http\Requests\v0\Routes\UpdateRouteStopRequest;
use Illuminate\Http\JsonResponse;

class RouteController extends Controller
{
    protected RouteService $routeService;
    protected RouteStopService $stopService;

    public function __construct(RouteService $routeService, RouteStopService $stopService)
    {
        $this->routeService = $routeService;
        $this->stopService = $stopService;
    }

    // Route endpoints

    /**
     * List all routes
     */
    public function index(): JsonResponse
    {
        return $this->routeService->all();
    }

    /**
     * Store a new route
     */
    public function store(StoreRouteRequest $request): JsonResponse
    {
        return $this->routeService->create($request->validated());
    }

    /**
     * Show a single route
     */
    public function show(string $id): JsonResponse
    {
        return $this->routeService->find($id);
    }

    /**
     * Update a route
     */
    public function update(UpdateRouteRequest $request, string $id): JsonResponse
    {
        return $this->routeService->update($id, $request->validated());
    }

    /**
     * Delete a route
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->routeService->delete($id);
    }

    /**
     * Get routes by status
     */
    public function byStatus(string $status): JsonResponse
    {
        return $this->routeService->findByStatus($status);
    }

    // RouteStop endpoints

    /**
     * List all route stops
     */
    public function stopsIndex(): JsonResponse
    {
        return $this->stopService->all();
    }

    /**
     * Store a new route stop
     */
    public function stopsStore(StoreRouteStopRequest $request): JsonResponse
    {
        return $this->stopService->create($request->validated());
    }

    /**
     * Show a single route stop
     */
    public function stopsShow(string $id): JsonResponse
    {
        return $this->stopService->find($id);
    }

    /**
     * Update a route stop
     */
    public function stopsUpdate(UpdateRouteStopRequest $request, string $id): JsonResponse
    {
        return $this->stopService->update($id, $request->validated());
    }

    /**
     * Delete a route stop
     */
    public function stopsDestroy(string $id): JsonResponse
    {
        return $this->stopService->delete($id);
    }

    /**
     * Search route stops by name
     */
    public function stopsByName(string $name): JsonResponse
    {
        return $this->stopService->findByName($name);
    }
}