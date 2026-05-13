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
        path: "/v0/auth/customers/login",
        summary: "Customer Login",
        tags: ["Customer Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "customer@neutek.local"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Login successful"),
            new OA\Response(response: 401, description: "Invalid credentials")
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->customerAuthService->login($request->validated());
    }

    #[OA\Post(
        path: "/v0/auth/customers/register",
        summary: "Customer Registration",
        tags: ["Customer Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john.doe@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Registration successful"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->customerAuthService->register($request->validated());
    }

    #[OA\Post(
        path: "/v0/auth/customers/password/request",
        summary: "Request Customer Password Reset",
        tags: ["Customer Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "customer@neutek.local")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Reset link sent"),
            new OA\Response(response: 404, description: "Customer not found")
        ]
    )]
    public function requestPasswordReset(RequestPasswordResetRequest $request): JsonResponse
    {
        return $this->customerAuthService->requestPasswordReset($request->input('email'));
    }

    #[OA\Post(
        path: "/v0/auth/customers/password/reset",
        summary: "Reset Customer Password",
        tags: ["Customer Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "customer@neutek.local"),
                    new OA\Property(property: "token", type: "string", example: "123456"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "newpassword123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "newpassword123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Password reset successful"),
            new OA\Response(response: 400, description: "Invalid token or data")
        ]
    )]
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->customerAuthService->resetPassword($request->validated());
    }

    #[OA\Post(
        path: "/v0/auth/customers/logout",
        summary: "Customer Logout",
        tags: ["Customer Auth"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(response: 200, description: "Logged out successfully")
        ]
    )]
    public function logout(): JsonResponse
    {
        return $this->customerAuthService->logout();
    }
}
