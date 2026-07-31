<?php

namespace App\Http\Controllers\v0\Auth\PlatformManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Auth\PlatformManager\LoginRequest;
use App\Http\Requests\v0\Auth\PlatformManager\RequestPasswordResetRequest;
use App\Http\Requests\v0\Auth\PlatformManager\ResetPasswordRequest;
use App\Http\Requests\v0\Auth\PlatformManager\ChangePasswordRequest;
use App\Modules\v0\Auth\Services\PlatformManager\PlatformManagerAuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(
        protected PlatformManagerAuthService $authService
    ) {
    }

    #[OA\Post(
        path: "/v0/auth/platform-managers/login",
        summary: "Platform Manager Login",
        tags: ["Platform Operator Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@gmail.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "12345")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    example: [
                        "data" => [
                            "token" => "a2636ba5-10c4-4c2c-87ef-51c6b526e55f|F7YA08MSz0P1TcRvJijUIP1Q6rOOEByUtmfX1jp26c4a5496",
                            "user" => [
                                "id" => "a2614e64-d087-488d-87ff-52e5437f932f",
                                "email" => "admin@gmail.com",
                                "first_name" => "System",
                                "last_name" => "Admin",
                                "phone_number" => "+233200000000",
                                "active" => true,
                                "role" => "admin",
                            ],
                        ],
                        "status" => "success",
                    ],
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "token", type: "string", example: "1|platform_sanctum_token_example"),
                                new OA\Property(
                                    property: "user",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "00000000-0000-0000-0000-000000000001"),
                                        new OA\Property(property: "email", type: "string", format: "email", example: "admin@gmail.com"),
                                        new OA\Property(property: "first_name", type: "string", example: "System"),
                                        new OA\Property(property: "last_name", type: "string", example: "Admin"),
                                        new OA\Property(property: "phone_number", type: "string", nullable: true, example: "+233200000000"),
                                        new OA\Property(property: "active", type: "boolean", example: true),
                                        new OA\Property(property: "role", type: "string", example: "admin")
                                    ]
                                )
                            ]
                        ),
                        new OA\Property(property: "status", type: "string", example: "success")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials", content: new OA\JsonContent())
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    #[OA\Post(
        path: "/v0/auth/platform-managers/logout",
        summary: "Platform Manager Logout",
        tags: ["Platform Operator Auth"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(response: 200, description: "Logged out successfully")
        ]
    )]
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }

    #[OA\Post(
        path: "/v0/auth/platform-managers/password/request",
        summary: "Request Password Reset",
        tags: ["Platform Operator Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@gmail.com")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Reset link sent"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function requestPasswordReset(RequestPasswordResetRequest $request): JsonResponse
    {
        return $this->authService->requestPasswordReset($request->input('email'));
    }

    #[OA\Post(
        path: "/v0/auth/platform-managers/password/reset",
        summary: "Reset Password",
        tags: ["Platform Operator Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@gmail.com"),
                    new OA\Property(property: "token", type: "string", example: "123456"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "12345"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "12345")
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
        return $this->authService->resetPassword($request->validated());
    }

    #[OA\Post(
        path: "/v0/auth/platform-managers/change-password",
        summary: "Change Platform Manager Password",
        tags: ["Platform Operator Auth"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "current_password", type: "string", format: "password", example: "12345"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "12345"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "12345")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Password changed successfully", content: new OA\JsonContent()),
            new OA\Response(response: 422, description: "Validation error or incorrect current password", content: new OA\JsonContent()),
            new OA\Response(response: 401, description: "Unauthorized", content: new OA\JsonContent())
        ]
    )]
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        return $this->authService->changePassword($request->validated());
    }
}
