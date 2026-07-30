<?php

use App\Http\Controllers\v0\Auth\PlatformManager\AuthController;
use App\Http\Controllers\v0\Customers\CustomerController;
use App\Http\Controllers\v0\Users\AssetController as UserAssetController;
use App\Http\Controllers\v0\Fees\FeeController;
use App\Http\Controllers\v0\Rates\RateController;
use App\Http\Controllers\v0\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v0/auth/platform-managers')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/request', [AuthController::class, 'requestPasswordReset']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);

    Route::group(['middleware' => 'auth:sanctum,platform_managers'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});

Route::middleware('auth:sanctum,platform_managers')->group(function () {
    Route::prefix('v0/assets')->group(function () {
        Route::get('/wallets', [UserAssetController::class, 'wallets']);
        Route::get('/withdrawals', [UserAssetController::class, 'withdrawals']);
        Route::post('/withdrawals/{id}/approve', [UserAssetController::class, 'approve']);
        Route::get('/transactions', [UserAssetController::class, 'transactions']);
        Route::get('/quotes', [UserAssetController::class, 'quotes']);
        Route::get('/prices/current', [UserAssetController::class, 'currentPrice']);
    });

    Route::prefix('v0/rates')->group(function () {
        Route::get('/', [RateController::class, 'index']);
        Route::post('/', [RateController::class, 'store']);
    });

    Route::prefix('v0/fees')->group(function () {
        Route::get('/', [FeeController::class, 'index']);
        Route::post('/', [FeeController::class, 'store']);
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
