<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Auth\LoginRequest;
use App\Http\Requests\v0\Auth\RegisterRequest;
use App\Http\Requests\v0\Passenger\PassengerRegisterRequest;
use App\Http\Requests\v0\Passenger\GoogleCodeRequest;
use App\Http\Requests\v0\Passenger\GoogleTokenRequest;
use App\Modules\v0\Users\Services\AuthService;
use App\Modules\v0\Users\Services\PassangerAuthService;
use App\Modules\v0\Users\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected PassangerAuthService $passengerAuthService;
    protected UserService $userService;

    public function __construct(
        AuthService $authService,
        PassangerAuthService $passengerAuthService,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->passengerAuthService = $passengerAuthService;
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

    // ================== Passenger Auth Endpoints ==================

    /**
     * Passenger login.
     */
    public function passengerLogin(Request $request): JsonResponse
    {
        $username = $request->input('username');
        $password = $request->input('password');

        return $this->passengerAuthService->login([
            'username' => $username,
            'password' => $password,
        ]);
    }

    /**
     * Passenger manual registration.
     */
    public function passengerRegister(PassengerRegisterRequest $request): JsonResponse
    {
        return $this->passengerAuthService->register($request->validated());
    }

    /**
     * Google OAuth code sign-in for passengers.
     */
    public function passengerGoogleCodeSignIn(GoogleCodeRequest $request): JsonResponse
    {
        return $this->passengerAuthService->googleCodeSignIn($request->input('code'));
    }

    /**
     * Google ID token sign-in for passengers.
     */
    public function passengerGoogleTokenSignIn(GoogleTokenRequest $request): JsonResponse
    {
        return $this->passengerAuthService->googleTokenSignIn($request->input('id_token'));
    }

    /**
     * Get Google auth URL for passengers.
     */
    public function passengerGoogleAuthUrl(): JsonResponse
    {
        return $this->passengerAuthService->getGoogleAuthUrl();
    }

    /**
     * Passenger logout.
     */
    public function passengerLogout(): JsonResponse
    {
        return $this->passengerAuthService->logout();
    }
}