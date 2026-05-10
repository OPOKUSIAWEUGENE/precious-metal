<?php

namespace App\Http\Controllers\v0\Auth\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Auth\Customer\GoogleCodeRequest;
use App\Http\Requests\v0\Auth\Customer\GoogleTokenRequest;
use App\Http\Requests\v0\Auth\Customer\LoginRequest;
use App\Http\Requests\v0\Auth\Customer\RegisterRequest;
use App\Modules\v0\Auth\Services\Customer\CustomerAuthService;
use Illuminate\Http\JsonResponse;

class CustomerAuthController extends Controller
{
    protected CustomerAuthService $customerAuthService;

    public function __construct(CustomerAuthService $customerAuthService)
    {
        $this->customerAuthService = $customerAuthService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->customerAuthService->login($request->validated());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->customerAuthService->register($request->validated());
    }

    public function googleCodeSignIn(GoogleCodeRequest $request): JsonResponse
    {
        return $this->customerAuthService->googleCodeSignIn($request->input('code'));
    }

    public function googleTokenSignIn(GoogleTokenRequest $request): JsonResponse
    {
        return $this->customerAuthService->googleTokenSignIn($request->input('id_token'));
    }

    public function googleAuthUrl(): JsonResponse
    {
        return $this->customerAuthService->getGoogleAuthUrl();
    }

    public function logout(): JsonResponse
    {
        return $this->customerAuthService->logout();
    }
}
