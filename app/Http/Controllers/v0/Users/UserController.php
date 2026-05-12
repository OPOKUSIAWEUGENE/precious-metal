<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\User\StoreUserRequest;
use App\Http\Requests\v0\User\UpdateUserRequest;
use App\Modules\v0\Users\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    // Listing
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);

        return $this->service->paginate((int) $perPage);
    }

    // CRUD
    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    // Lookup
    public function findByEmail(string $email): JsonResponse
    {
        return $this->service->findByEmail($email);
    }

    // Status
    public function deactivate(string $id): JsonResponse
    {
        return $this->service->deactivate($id);
    }

    public function activate(string $id): JsonResponse
    {
        return $this->service->activate($id);
    }
}
