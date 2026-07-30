<?php

use App\Http\Controllers\v0\Auth\Customer\CustomerAuthController;
use App\Http\Controllers\v0\Customers\AssetController as CustomerAssetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v0/customers/auth')->group(function () {
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/password/request', [CustomerAuthController::class, 'requestPasswordReset']);
    Route::post('/password/reset', [CustomerAuthController::class, 'resetPassword']);

    Route::group(['middleware' => 'auth:sanctum,customers'], function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
    });
});

Route::prefix('v0/customers/assets')->middleware('auth:sanctum,customers')->group(function () {
    Route::get('/wallets', [CustomerAssetController::class, 'wallets']);
    Route::get('/rate', [CustomerAssetController::class, 'rate']);
    Route::get('/balance', [CustomerAssetController::class, 'balance']);
    Route::get('/transactions', [CustomerAssetController::class, 'transactions']);
    Route::get('/transactions/{reference}', [CustomerAssetController::class, 'transaction']);
    Route::get('/prices/current', [CustomerAssetController::class, 'currentPrice']);
    Route::post('/quotes', [CustomerAssetController::class, 'quote']);
    Route::get('/quotes/{reference}', [CustomerAssetController::class, 'quoteShow']);
    Route::post('/transfers/recipient-enquiry', [CustomerAssetController::class, 'recipientEnquiry']);
    Route::post('/transfers/quote', [CustomerAssetController::class, 'transferQuote']);
    Route::post('/transfers', [CustomerAssetController::class, 'transfer']);
    Route::post('/sales/quote', [CustomerAssetController::class, 'saleQuote']);
    Route::post('/sales', [CustomerAssetController::class, 'sale']);
    Route::post('/purchases/quote', [CustomerAssetController::class, 'purchaseQuote']);
    Route::post('/purchases', [CustomerAssetController::class, 'purchase']);
    Route::post('/withdrawals', [CustomerAssetController::class, 'store']);
});
