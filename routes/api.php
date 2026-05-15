<?php

use App\Http\Controllers\v0\Auth\Customer\CustomerAuthController;
use App\Http\Controllers\v0\Auth\PlatformManager\AuthController;
use App\Http\Controllers\v0\Customers\CustomerController;
use App\Http\Controllers\v0\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v0/auth')->group(function () {
    Route::group(['prefix' => 'platform-managers'], function () {
        Route::post('/login', [AuthController::class, 'login']);

        Route::post('/password/request', [AuthController::class, 'requestPasswordReset']);
        Route::post('/password/reset', [AuthController::class, 'resetPassword']);
        
        Route::group(['middleware' => 'auth:sanctum,platform_managers'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });

    Route::group(['prefix' => 'customers'], function () {
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
        Route::post('/register', [CustomerAuthController::class, 'register']);
        Route::post('/password/request', [CustomerAuthController::class, 'requestPasswordReset']);
        Route::post('/password/reset', [CustomerAuthController::class, 'resetPassword']);

        Route::group(['middleware' => 'auth:sanctum,customers'], function () {
            Route::post('/logout', [CustomerAuthController::class, 'logout']);
        });
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v0/users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::post('/{id}/password/request', [UserController::class, 'requestPasswordReset']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/email/{email}', [UserController::class, 'findByEmail']);
        Route::put('/{id}/deactivate', [UserController::class, 'deactivate']);
        Route::put('/{id}/activate', [UserController::class, 'activate']);
    });

    Route::prefix('v0/customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::post('/{id}/password/request', [CustomerController::class, 'requestPasswordReset']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
        Route::get('/email/{email}', [CustomerController::class, 'findByEmail']);
        Route::put('/{id}/deactivate', [CustomerController::class, 'deactivate']);
        Route::put('/{id}/activate', [CustomerController::class, 'activate']);
    });
});
