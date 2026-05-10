<?php

namespace App\Http\Controllers\v0\Auth\PlatformOperator;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Auth\PlatformOperator\LoginRequest;
use App\Http\Requests\v0\Auth\PlatformOperator\RegisterRequest;
use App\Modules\v0\Auth\Services\PlatformOperator\AuthService;
use App\Modules\v0\Users\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected UserService $userService;

    public function __construct(
        AuthService $authService,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    public function loginWithEmail(Request $request): JsonResponse
    {
        return $this->authService->loginWithEmail(
            $request->input('email'),
            $request->input('password')
        );
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request->validated());
    }

    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }

    public function me(): JsonResponse
    {
        return $this->authService->me();
    }
}
