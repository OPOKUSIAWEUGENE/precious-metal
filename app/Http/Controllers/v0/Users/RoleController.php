<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\User\StoreRoleRequest;
use App\Http\Requests\v0\User\UpdateRoleRequest;
use App\Modules\v0\Users\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of all roles.
     */
    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    /**
     * Display the specified role.
     */
    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Remove the specified role.
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }
}
