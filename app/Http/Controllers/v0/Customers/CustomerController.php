<?php

namespace App\Http\Controllers\v0\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Customer\StoreCustomerRequest;
use App\Http\Requests\v0\Customer\UpdateCustomerRequest;
use App\Modules\v0\Customers\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CustomerController extends Controller
{
    protected CustomerService $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }

    #[OA\Get(
        path: "/v0/customers",
        summary: "List all customers (Admin)",
        tags: ["Customer"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "per_page", in: "query", schema: new OA\Schema(type: "integer", default: 15))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);

        return $this->service->paginate((int) $perPage);
    }

    #[OA\Post(
        path: "/v0/customers",
        summary: "Create a new customer (Admin)",
        tags: ["Customer"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "first_name", type: "string"),
                    new OA\Property(property: "last_name", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "phone", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Created")
        ]
    )]
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    #[OA\Get(
        path: "/v0/customers/{id}",
        summary: "Get customer details (Admin)",
        tags: ["Customer"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateCustomerRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    #[OA\Post(
        path: "/v0/customers/{id}/password/request",
        summary: "Request a password reset for a customer",
        tags: ["Customers"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Reset token generated"),
            new OA\Response(response: 404, description: "Customer not found")
        ]
    )]
    public function requestPasswordReset(string $id): JsonResponse
    {
        return $this->service->requestPasswordReset($id);
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    public function findByEmail(string $email): JsonResponse
    {
        return $this->service->findByEmail($email);
    }

    #[OA\Put(
        path: "/v0/customers/{id}/deactivate",
        summary: "Deactivate a customer (Admin)",
        tags: ["Customers"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Customer deactivated successfully")
        ]
    )]
    public function deactivate(string $id): JsonResponse
    {
        return $this->service->deactivate($id);
    }

    #[OA\Put(
        path: "/v0/customers/{id}/activate",
        summary: "Activate a customer (Admin)",
        tags: ["Customers"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Customer activated successfully")
        ]
    )]
    public function activate(string $id): JsonResponse
    {
        return $this->service->activate($id);
    }
}
