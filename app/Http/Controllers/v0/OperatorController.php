<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Operator\StoreOperatorRequest;
use App\Http\Requests\v0\Operator\UpdateOperatorRequest;
use App\Modules\v0\Operator\Services\OperatorService;
use Illuminate\Http\JsonResponse;

class OperatorController extends Controller
{
    protected OperatorService $service;

    public function __construct(OperatorService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    public function store(StoreOperatorRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateOperatorRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }
}
