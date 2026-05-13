<?php

namespace App\Http\Controllers\v0\Auth\PlatformManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Auth\PlatformManager\LoginRequest;
use App\Http\Requests\v0\Auth\PlatformManager\RequestPasswordResetRequest;
use App\Http\Requests\v0\Auth\PlatformManager\ResetPasswordRequest;
use App\Modules\v0\Auth\Services\PlatformManager\PlatformManagerAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected PlatformManagerAuthService $authService
    ) {
    }

    // Authentication
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }



    // Session
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }



    // Password reset
    public function requestPasswordReset(RequestPasswordResetRequest $request): JsonResponse
    {
        return $this->authService->requestPasswordReset($request->input('email'));
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->authService->resetPassword($request->validated());
    }
}
