<?php

use App\Http\Controllers\v0\Auth\Customer\CustomerAuthController;
use App\Http\Controllers\v0\Auth\PlatformManager\AuthController;
use App\Http\Controllers\v0\Users\ProfileController;
use App\Http\Controllers\v0\Users\RoleController;
use App\Http\Controllers\v0\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v0/auth')->group(function () {
    Route::group(['prefix' => 'platform-managers'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/login/email', [AuthController::class, 'loginWithEmail']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/password/request', [AuthController::class, 'requestPasswordReset']);
        Route::post('/password/reset', [AuthController::class, 'resetPassword']);
    });

    Route::group(['prefix' => 'customers'], function () {
        Route::post('/login', [CustomerAuthController::class, 'login']);
        Route::post('/register', [CustomerAuthController::class, 'register']);
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/me', [CustomerAuthController::class, 'me']);
        Route::post('/password/request', [CustomerAuthController::class, 'requestPasswordReset']);
        Route::post('/password/reset', [CustomerAuthController::class, 'resetPassword']);
    });
});

Route::prefix('v0/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/email/{email}', [UserController::class, 'findByEmail']);
    Route::post('/{userId}/profiles', [UserController::class, 'addProfile']);
    Route::put('/{userId}/current-profile', [UserController::class, 'setCurrentProfile']);
    Route::get('/profile-type/{type}', [UserController::class, 'byProfileType']);
    Route::put('/{id}/deactivate', [UserController::class, 'deactivate']);
    Route::put('/{id}/activate', [UserController::class, 'activate']);
});

Route::prefix('v0/profiles')->group(function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::post('/', [ProfileController::class, 'store']);
    Route::get('/{id}', [ProfileController::class, 'show']);
    Route::put('/{id}', [ProfileController::class, 'update']);
    Route::delete('/{id}', [ProfileController::class, 'destroy']);
    Route::get('/user/{userId}', [ProfileController::class, 'byUser']);
    Route::get('/type/{type}', [ProfileController::class, 'byType']);
    Route::put('/user/{userId}/current', [ProfileController::class, 'setCurrent']);
});

Route::prefix('v0/roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
    Route::get('/name/{name}', [RoleController::class, 'findByName']);
});
