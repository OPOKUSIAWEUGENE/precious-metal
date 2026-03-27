<?php

use App\Http\Controllers\v0\OperatorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v0/operators')->group(function () {
    Route::get('/', [OperatorController::class, 'index']);
    Route::post('/', [OperatorController::class, 'store']);
    Route::get('/{id}', [OperatorController::class, 'show']);
    Route::put('/{id}', [OperatorController::class, 'update']);
    Route::delete('/{id}', [OperatorController::class, 'destroy']);
});
