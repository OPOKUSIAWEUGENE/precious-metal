<?php

use App\Http\Controllers\v0\Auth\Customer\CustomerAuthController;
use App\Http\Controllers\v0\Customers\AssetController as CustomerAssetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v0/auth/customers')->group(function () {
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/password/request', [CustomerAuthController::class, 'requestPasswordReset']);
    Route::post('/password/reset', [CustomerAuthController::class, 'resetPassword']);

    Route::group(['middleware' => 'auth:sanctum,customers'], function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
    });
});

Route::prefix('v0/assets')->middleware('auth:sanctum,customers')->group(function () {
    Route::get('/wallets', [CustomerAssetController::class, 'wallets']);
    Route::get('/rate', [CustomerAssetController::class, 'rate']);
    Route::post('/withdrawals', [CustomerAssetController::class, 'store']);
});
