<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\v0\Debug\MailPreviewController;

Route::prefix('mail-previews')->group(function () {
    Route::get('/', [MailPreviewController::class, 'index']);
    Route::get('/welcome', [MailPreviewController::class, 'previewWelcome']);
    Route::get('/password-reset', [MailPreviewController::class, 'previewPasswordReset']);
    Route::post('/send', [MailPreviewController::class, 'send']);
});
