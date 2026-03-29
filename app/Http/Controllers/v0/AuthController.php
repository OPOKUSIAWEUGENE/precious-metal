<?php

namespace App\Http\Controllers\v0;

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
use OpenApi\Annotations\OA;
use OpenApi\Annotations\Route;

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
     *
     * @OA\Post(
     *     path="/api/v0/auth/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Authenticate user with email or phone and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="admin@rushour.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    /**
     * Login with email specifically (for operators).
     *
     * @OA\Post(
     *     path="/api/v0/auth/login/email",
     *     tags={"Authentication"},
     *     summary="Login with email",
     *     description="Login using email address (for operators and platform admins)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="operator@rushour.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful login", @OA\JsonContent(ref="#/components/schemas/AuthResponse")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function loginWithEmail(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');

        return $this->authService->loginWithEmail($email, $password);
    }

    /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/api/v0/auth/register",
     *     tags={"Authentication"},
     *     summary="Register new user",
     *     description="Register a new user account with role selection",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "first_name", "last_name", "phone_number"},
     *             @OA\Property(property="email", type="string", example="newuser@rushour.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="phone_number", type="string", example="+233200000000")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created", @OA\JsonContent(ref="#/components/schemas/AuthResponse")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request->validated());
    }

    /**
     * Logout.
     *
     * @OA\Post(
     *     path="/api/v0/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     description="Logout current authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Successful logout", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }

    /**
     * Get current authenticated user.
     *
     * @OA\Get(
     *     path="/api/v0/auth/me",
     *     tags={"Authentication"},
     *     summary="Get current user",
     *     description="Get the currently authenticated user's profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/PlatformOperator")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function me(): JsonResponse
    {
        return $this->authService->me();
    }

    // ================== Passenger Auth Endpoints ==================

    /**
     * Passenger login.
     *
     * @OA\Post(
     *     path="/api/v0/auth/passenger/login",
     *     tags={"Passenger Authentication"},
     *     summary="Passenger login",
     *     description="Login for passengers using phone number or email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="username", type="string", example="+233200000003"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful login", @OA\JsonContent(ref="#/components/schemas/AuthResponse")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
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
     *
     * @OA\Post(
     *     path="/api/v0/auth/passenger/register",
     *     tags={"Passenger Authentication"},
     *     summary="Passenger registration",
     *     description="Register a new passenger account",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="passenger@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="first_name", type="string", example="Jane"),
     *             @OA\Property(property="last_name", type="string", example="Smith"),
     *             @OA\Property(property="phone_number", type="string", example="+233200000004")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Passenger created", @OA\JsonContent(ref="#/components/schemas/AuthResponse")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
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