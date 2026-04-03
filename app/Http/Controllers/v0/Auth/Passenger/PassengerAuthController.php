<?php

namespace App\Http\Controllers\v0\Auth\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Passenger\PassengerRegisterRequest;
use App\Http\Requests\v0\Passenger\GoogleCodeRequest;
use App\Http\Requests\v0\Passenger\GoogleTokenRequest;
use App\Modules\v0\Auth\Services\Passenger\PassangerAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PassengerAuthController extends Controller
{
    protected PassangerAuthService $passengerAuthService;

    public function __construct(PassangerAuthService $passengerAuthService)
    {
        $this->passengerAuthService = $passengerAuthService;
    }

    /**
     * Passenger login.
     */
    public function login(Request $request): JsonResponse
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
    public function register(PassengerRegisterRequest $request): JsonResponse
    {
        return $this->passengerAuthService->register($request->validated());
    }

    /**
     * Google OAuth code sign-in for passengers.
     */
    public function googleCodeSignIn(GoogleCodeRequest $request): JsonResponse
    {
        return $this->passengerAuthService->googleCodeSignIn($request->input('code'));
    }

    /**
     * Google ID token sign-in for passengers.
     */
    public function googleTokenSignIn(GoogleTokenRequest $request): JsonResponse
    {
        return $this->passengerAuthService->googleTokenSignIn($request->input('id_token'));
    }

    /**
     * Get Google auth URL for passengers.
     */
    public function googleAuthUrl(): JsonResponse
    {
        return $this->passengerAuthService->getGoogleAuthUrl();
    }

    /**
     * Passenger logout.
     */
    public function logout(): JsonResponse
    {
        return $this->passengerAuthService->logout();
    }
}
