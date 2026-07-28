<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\User\StoreUserRequest;
use App\Http\Requests\v0\User\UpdateUserRequest;
use App\Modules\v0\Users\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    #[OA\Get(
        path: "/v0/users",
        summary: "List all platform users",
        tags: ["Users"],
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
        path: "/v0/users",
        summary: "Create a new platform user",
        tags: ["Users"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "first_name", type: "string"),
                    new OA\Property(property: "last_name", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "change_password", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Created")
        ]
    )]
    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    #[OA\Get(
        path: "/v0/users/{id}",
        summary: "Get user details",
        tags: ["Users"],
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
        path: "/v0/users/{id}",
        summary: "Update user details",
        tags: ["Users"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "password", type: "string", format: "password"),
                    new OA\Property(property: "change_password", type: "boolean"),
                    new OA\Property(property: "first_name", type: "string"),
                    new OA\Property(property: "last_name", type: "string"),
                    new OA\Property(property: "phone_number", type: "string"),
                    new OA\Property(property: "profile_img", type: "string"),
                    new OA\Property(property: "role", type: "string"),
                    new OA\Property(property: "active", type: "boolean")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Updated")
        ]
    )]
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    #[OA\Post(
        path: "/v0/users/{id}/password/request",
        summary: "Request a password reset for a user",
        tags: ["Users"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Reset token generated"),
            new OA\Response(response: 404, description: "User not found")
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
        path: "/v0/users/{id}/deactivate",
        summary: "Deactivate a user",
        tags: ["Users"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "User deactivated successfully")
        ]
    )]
    public function deactivate(string $id): JsonResponse
    {
        return $this->service->deactivate($id);
    }

    #[OA\Put(
        path: "/v0/users/{id}/activate",
        summary: "Activate a user",
        tags: ["Users"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "User activated successfully")
        ]
    )]
    public function activate(string $id): JsonResponse
    {
        return $this->service->activate($id);
    }
}
