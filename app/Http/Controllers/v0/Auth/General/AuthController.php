<?php

namespace App\Http\Controllers\v0\Auth\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Auth\LoginRequest;
use App\Http\Requests\v0\Auth\RegisterRequest;
use App\Modules\v0\Auth\Services\General\AuthService;
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

    /**
     * Login with email/phone and password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    /**
     * Login with email specifically (for operators).
     */
    public function loginWithEmail(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');

        return $this->authService->loginWithEmail($email, $password);
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request->validated());
    }

    /**
     * Logout.
     */
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }

    /**
     * Get current authenticated user.
     */
    public function me(): JsonResponse
    {
        return $this->authService->me();
    }
}