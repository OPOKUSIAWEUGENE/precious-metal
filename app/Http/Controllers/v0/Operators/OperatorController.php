<?php

namespace App\Http\Controllers\v0\Operators;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Operator\StoreOperatorRequest;
use App\Http\Requests\v0\Operator\UpdateOperatorRequest;
use App\Modules\v0\Operators\Services\OperatorService;
use Illuminate\Http\JsonResponse;

class OperatorController extends Controller
{
    protected OperatorService $service;

    public function __construct(OperatorService $service)
    {
        $this->service = $service;
    }

    /**
     * List all operators.
     */
    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    /**
     * Create a new operator.
     */
    public function store(StoreOperatorRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    /**
     * Get operator details.
     */
    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    /**
     * Update an operator.
     */
    public function update(UpdateOperatorRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Delete an operator.
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }
}
