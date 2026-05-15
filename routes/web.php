<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'timestamp' => now()->toIso8601String(),
        'status' => 'running',
    ]);
});

use App\Http\Controllers\v0\Debug\MailPreviewController;

Route::prefix('mail-previews')->group(function () {
    Route::get('/', [MailPreviewController::class, 'index']);
    Route::get('/welcome', [MailPreviewController::class, 'previewWelcome']);
    Route::get('/password-reset', [MailPreviewController::class, 'previewPasswordReset']);
    Route::post('/send', [MailPreviewController::class, 'send']);
});
