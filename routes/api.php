<?php

use App\Http\Controllers\v0\OperatorController;
use App\Http\Controllers\v0\UserController;
use App\Http\Controllers\v0\ProfileController;
use App\Http\Controllers\v0\RoleController;
use Illuminate\Support\Facades\Route;

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
