<?php

namespace App\Http\Controllers\v0\Auth\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Auth\Customer\LoginRequest;
use App\Http\Requests\v0\Auth\Customer\RequestPasswordResetRequest;
use App\Http\Requests\v0\Auth\Customer\RegisterRequest;
use App\Http\Requests\v0\Auth\Customer\ResetPasswordRequest;
use App\Modules\v0\Auth\Services\Customer\CustomerAuthService;
use Illuminate\Http\JsonResponse;

class CustomerAuthController extends Controller
{
    protected CustomerAuthService $customerAuthService;

    public function __construct(CustomerAuthService $customerAuthService)
    {
        $this->customerAuthService = $customerAuthService;
    }

    // Authentication
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->customerAuthService->login($request->validated());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->customerAuthService->register($request->validated());
    }

    // Password reset
    public function requestPasswordReset(RequestPasswordResetRequest $request): JsonResponse
    {
        return $this->customerAuthService->requestPasswordReset($request->input('email'));
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->customerAuthService->resetPassword($request->validated());
    }

    // Session
    public function logout(): JsonResponse
    {
        return $this->customerAuthService->logout();
    }

    public function me(): JsonResponse
    {
        return $this->customerAuthService->me();
    }
}
