<?php

use App\Http\Controllers\v0\Auth\Customer\CustomerAuthController;
use App\Http\Controllers\v0\Customers\GoldWalletController as CustomerGoldWalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('v0/auth/customers')->group(function () {
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/password/request', [CustomerAuthController::class, 'requestPasswordReset']);
    Route::post('/password/reset', [CustomerAuthController::class, 'resetPassword']);

    Route::group(['middleware' => 'auth:sanctum,customers'], function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/gold-wallets', [CustomerGoldWalletController::class, 'index']);
    });
});
