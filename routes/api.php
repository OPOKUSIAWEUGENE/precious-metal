<?php

use App\Http\Controllers\v0\OperatorController;
use App\Http\Controllers\v0\UserController;
use App\Http\Controllers\v0\ProfileController;
use App\Http\Controllers\v0\RoleController;
use App\Http\Controllers\v0\VehicleController;
use App\Http\Controllers\v0\DriverVehicleController;
use App\Http\Controllers\v0\DriverProfileController;
use App\Http\Controllers\v0\TripController;
use App\Http\Controllers\v0\BookingController;
use App\Http\Controllers\v0\RouteController;
use App\Http\Controllers\v0\AuthController;
use App\Http\Controllers\v0\PassengerTripController;
use App\Http\Controllers\v0\FinanceController;
use Illuminate\Support\Facades\Route;

// ================== Authentication Routes ==================

Route::prefix('v0/auth')->group(function () {
    // General auth
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/email', [AuthController::class, 'loginWithEmail']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Passenger auth
    Route::post('/passenger/login', [AuthController::class, 'passengerLogin']);
    Route::post('/passenger/register', [AuthController::class, 'passengerRegister']);
    Route::post('/passenger/google/code', [AuthController::class, 'passengerGoogleCodeSignIn']);
    Route::post('/passenger/google/token', [AuthController::class, 'passengerGoogleTokenSignIn']);
    Route::get('/passenger/google/url', [AuthController::class, 'passengerGoogleAuthUrl']);
    Route::post('/passenger/logout', [AuthController::class, 'passengerLogout']);
});

// ================== Passenger Public Routes ==================

Route::prefix('v0/passenger/trips')->group(function () {
    Route::get('/featured', [PassengerTripController::class, 'featured']);
    Route::get('/search', [PassengerTripController::class, 'search']);
});

// ================== Protected Routes (require auth) ==================

Route::prefix('v0/operators')->group(function () {
    Route::get('/', [OperatorController::class, 'index']);
    Route::post('/', [OperatorController::class, 'store']);
    Route::get('/{id}', [OperatorController::class, 'show']);
    Route::put('/{id}', [OperatorController::class, 'update']);
    Route::delete('/{id}', [OperatorController::class, 'destroy']);
});

Route::prefix('v0/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/email/{email}', [UserController::class, 'findByEmail']);
});

Route::prefix('v0/profiles')->group(function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::post('/', [ProfileController::class, 'store']);
    Route::get('/{id}', [ProfileController::class, 'show']);
    Route::put('/{id}', [ProfileController::class, 'update']);
    Route::delete('/{id}', [ProfileController::class, 'destroy']);
    Route::get('/user/{userId}', [ProfileController::class, 'byUser']);
    Route::get('/user/{userId}/current', [ProfileController::class, 'currentProfile']);
});

Route::prefix('v0/roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
    Route::get('/name/{name}', [RoleController::class, 'findByName']);
});

Route::prefix('v0/vehicles')->group(function () {
    Route::get('/', [VehicleController::class, 'index']);
    Route::post('/', [VehicleController::class, 'store']);
    Route::get('/{id}', [VehicleController::class, 'show']);
    Route::put('/{id}', [VehicleController::class, 'update']);
    Route::delete('/{id}', [VehicleController::class, 'destroy']);
    Route::get('/fleet/{fleetIdentifier}', [VehicleController::class, 'byFleetIdentifier']);
});

Route::prefix('v0/driver-vehicles')->group(function () {
    Route::get('/', [DriverVehicleController::class, 'index']);
    Route::post('/', [DriverVehicleController::class, 'store']);
    Route::get('/{id}', [DriverVehicleController::class, 'show']);
    Route::delete('/{id}', [DriverVehicleController::class, 'destroy']);
    Route::get('/profile/{profileId}', [DriverVehicleController::class, 'byProfile']);
});

Route::prefix('v0/drivers')->group(function () {
    Route::get('/', [DriverProfileController::class, 'index']);
    Route::post('/', [DriverProfileController::class, 'store']);
    Route::get('/{id}', [DriverProfileController::class, 'show']);
    Route::put('/{id}', [DriverProfileController::class, 'update']);
});

Route::prefix('v0/trips')->group(function () {
    Route::get('/', [TripController::class, 'index']);
    Route::post('/', [TripController::class, 'store']);
    Route::get('/{id}', [TripController::class, 'show']);
    Route::put('/{id}', [TripController::class, 'update']);
    Route::delete('/{id}', [TripController::class, 'destroy']);
    Route::get('/route/{routeId}', [TripController::class, 'byRoute']);
    Route::get('/vehicle/{vehicleId}', [TripController::class, 'byVehicle']);
    Route::get('/driver/{driverProfileId}', [TripController::class, 'byDriver']);
});

Route::prefix('v0/bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::post('/', [BookingController::class, 'store']);
    Route::get('/{id}', [BookingController::class, 'show']);
    Route::put('/{id}', [BookingController::class, 'update']);
    Route::delete('/{id}', [BookingController::class, 'destroy']);
    Route::get('/trip/{tripId}', [BookingController::class, 'byTrip']);
    Route::get('/passenger/{passengerProfileId}', [BookingController::class, 'byPassenger']);
});

Route::prefix('v0/routes')->group(function () {
    Route::get('/', [RouteController::class, 'index']);
    Route::post('/', [RouteController::class, 'store']);
    Route::get('/{id}', [RouteController::class, 'show']);
    Route::put('/{id}', [RouteController::class, 'update']);
    Route::delete('/{id}', [RouteController::class, 'destroy']);
    Route::get('/status/{status}', [RouteController::class, 'byStatus']);
});

Route::prefix('v0/route-stops')->group(function () {
    Route::get('/', [RouteController::class, 'stopsIndex']);
    Route::post('/', [RouteController::class, 'stopsStore']);
    Route::get('/{id}', [RouteController::class, 'stopsShow']);
    Route::put('/{id}', [RouteController::class, 'stopsUpdate']);
    Route::delete('/{id}', [RouteController::class, 'stopsDestroy']);
    Route::get('/search/{name}', [RouteController::class, 'stopsByName']);
});

// ================== Finance Routes ==================

Route::prefix('v0/settlements')->group(function () {
    Route::get('/', [FinanceController::class, 'settlementsIndex']);
    Route::post('/', [FinanceController::class, 'settlementsStore']);
    Route::get('/{id}', [FinanceController::class, 'settlementsShow']);
    Route::put('/{id}', [FinanceController::class, 'settlementsUpdate']);
    Route::delete('/{id}', [FinanceController::class, 'settlementsDestroy']);
    Route::get('/operator/{operatorId}', [FinanceController::class, 'settlementsByOperator']);
    Route::get('/driver/{driverProfileId}', [FinanceController::class, 'settlementsByDriver']);
    Route::get('/status/{status}', [FinanceController::class, 'settlementsByStatus']);
    Route::get('/period/{periodStart}/{periodEnd}', [FinanceController::class, 'settlementsByPeriod']);
});

Route::prefix('v0/commissions')->group(function () {
    Route::get('/', [FinanceController::class, 'commissionsIndex']);
    Route::post('/', [FinanceController::class, 'commissionsStore']);
    Route::get('/{id}', [FinanceController::class, 'commissionsShow']);
    Route::put('/{id}', [FinanceController::class, 'commissionsUpdate']);
    Route::delete('/{id}', [FinanceController::class, 'commissionsDestroy']);
    Route::get('/trip/{tripId}', [FinanceController::class, 'commissionsByTrip']);
    Route::get('/operator/{operatorId}', [FinanceController::class, 'commissionsByOperator']);
    Route::get('/driver/{driverProfileId}', [FinanceController::class, 'commissionsByDriver']);
    Route::get('/status/{status}', [FinanceController::class, 'commissionsByStatus']);
    Route::get('/period/{periodStart}/{periodEnd}', [FinanceController::class, 'commissionsByPeriod']);
    Route::get('/sum/operator/{operatorId}/{periodStart}/{periodEnd}', [FinanceController::class, 'commissionsSumByOperator']);
    Route::get('/sum/driver/{driverProfileId}/{periodStart}/{periodEnd}', [FinanceController::class, 'commissionsSumByDriver']);
});
