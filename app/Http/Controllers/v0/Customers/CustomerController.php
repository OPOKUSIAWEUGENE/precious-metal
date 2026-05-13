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
        tags: ["Customers"],
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
        tags: ["Customers"],
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
        tags: ["Customers"],
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

    #[OA\Put(
        path: "/v0/customers/{id}",
        summary: "Update customer details (Admin)",
        tags: ["Customers"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Updated")
        ]
    )]
    public function update(UpdateCustomerRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    #[OA\Delete(
        path: "/v0/customers/{id}",
        summary: "Delete a customer (Admin)",
        tags: ["Customers"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Deleted")
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    public function findByEmail(string $email): JsonResponse
    {
        return $this->service->findByEmail($email);
    }

    public function deactivate(string $id): JsonResponse
    {
        return $this->service->deactivate($id);
    }

    public function activate(string $id): JsonResponse
    {
        return $this->service->activate($id);
    }
}
