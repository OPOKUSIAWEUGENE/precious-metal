<?php

namespace App\Http\Controllers\v0\Auth\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Auth\Customer\LoginRequest;
use App\Http\Requests\v0\Auth\Customer\RequestPasswordResetRequest;
use App\Http\Requests\v0\Auth\Customer\RegisterRequest;
use App\Http\Requests\v0\Auth\Customer\ResetPasswordRequest;
use App\Modules\v0\Auth\Services\Customer\CustomerAuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CustomerAuthController extends Controller
{
    protected CustomerAuthService $customerAuthService;

    public function __construct(CustomerAuthService $customerAuthService)
    {
        $this->customerAuthService = $customerAuthService;
    }

    #[OA\Post(
        path: "/v0/customers/auth/login",
        summary: "Customer Login",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "customer@gmail.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "12345")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "token", type: "string", example: "1|customer_sanctum_token_example"),
                                new OA\Property(
                                    property: "customer",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "00000000-0000-0000-0000-000000000001"),
                                        new OA\Property(property: "email", type: "string", format: "email", example: "customer@gmail.com"),
                                        new OA\Property(property: "first_name", type: "string", example: "John"),
                                        new OA\Property(property: "last_name", type: "string", example: "Customer"),
                                        new OA\Property(property: "date_of_birth", type: "string", nullable: true, example: "1990-01-01"),
                                        new OA\Property(property: "nationality", type: "string", nullable: true, example: "Ghanaian"),
                                        new OA\Property(property: "resident_address", type: "string", nullable: true, example: "123 Tech Street, Accra"),
                                        new OA\Property(property: "selfie", type: "string", nullable: true, example: null),
                                        new OA\Property(property: "phone_number", type: "string", example: "+233240000000"),
                                        new OA\Property(property: "kyc", type: "string", nullable: true, example: null),
                                        new OA\Property(property: "card_details", type: "string", nullable: true, example: null),
                                        new OA\Property(property: "active", type: "boolean", example: true)
                                    ]
                                )
                            ]
                        ),
                        new OA\Property(property: "status", type: "string", example: "success")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized", content: new OA\JsonContent()),
            new OA\Response(response: 404, description: "Not Found", content: new OA\JsonContent()),
            new OA\Response(response: 400, description: "Bad Request", content: new OA\JsonContent()),
            new OA\Response(response: 201, description: "Created", content: new OA\JsonContent())
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('Login attempt', $request->validated());
        return $this->customerAuthService->login($request->validated());
    }

    #[OA\Post(
        path: "/v0/customers/auth/register",
        summary: "Customer Registration",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "phone_number", type: "string", example: "+233240000000"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "12345"),
                    new OA\Property(property: "confirm_password", type: "string", format: "password", example: "12345"),
                    new OA\Property(property: "email", type: "string", format: "email", nullable: true, example: "customer@gmail.com"),
                    new OA\Property(property: "first_name", type: "string", nullable: true, example: "John"),
                    new OA\Property(property: "last_name", type: "string", nullable: true, example: "Customer"),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true, example: "1990-01-01"),
                    new OA\Property(property: "nationality", type: "string", nullable: true, example: "Ghanaian"),
                    new OA\Property(property: "resident_address", type: "string", nullable: true, example: "123 Tech Street, Accra"),
                    new OA\Property(property: "selfie", type: "string", nullable: true, example: null),
                    new OA\Property(property: "kyc", type: "array", nullable: true, example: null),
                    new OA\Property(property: "card_details", type: "array", nullable: true, example: null)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Registration successful", content: new OA\JsonContent()),
            new OA\Response(response: 400, description: "Validation error", content: new OA\JsonContent())
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->customerAuthService->register($request->validated());
    }

    #[OA\Post(
        path: "/v0/customers/auth/password/request",
        summary: "Request Customer Password Reset",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "customer@gmail.com")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Reset link sent", content: new OA\JsonContent()),
            new OA\Response(response: 404, description: "Customer not found", content: new OA\JsonContent())
        ]
    )]
    public function requestPasswordReset(RequestPasswordResetRequest $request): JsonResponse
    {
        return $this->customerAuthService->requestPasswordReset($request->input('email'));
    }

    #[OA\Post(
        path: "/v0/customers/auth/password/reset",
        summary: "Reset Customer Password",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "customer@gmail.com"),
                    new OA\Property(property: "token", type: "string", example: "123456"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "12345"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "12345")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Password reset successful", content: new OA\JsonContent()),
            new OA\Response(response: 400, description: "Invalid token or data", content: new OA\JsonContent())
        ]
    )]
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->customerAuthService->resetPassword($request->validated());
    }

    #[OA\Post(
        path: "/v0/customers/auth/logout",
        summary: "Customer Logout",
        tags: ["Auth"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(response: 200, description: "Logged out successfully", content: new OA\JsonContent())
        ]
    )]
    public function logout(): JsonResponse
    {
        return $this->customerAuthService->logout();
    }
}
