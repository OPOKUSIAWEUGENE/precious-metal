<?php

use App\Http\Controllers\ApiDocumentationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'timestamp' => now()->toIso8601String(),
        'status' => 'running',
    ]);
});

Route::get('/docs', [ApiDocumentationController::class, 'index'])->name('docs.index');
Route::view('/app-overview', 'app-overview')->name('docs.app-overview');
Route::get('/developer/modules', [ApiDocumentationController::class, 'modules'])->name('docs.modules');
Route::get('/developer/docs/{documentation}', [ApiDocumentationController::class, 'show'])
    ->whereIn('documentation', ['customer', 'platform'])
    ->name('docs.show');

use App\Http\Controllers\v0\Debug\MailPreviewController;

Route::prefix('mail-previews')->group(function () {
    Route::get('/', [MailPreviewController::class, 'index']);
    Route::get('/welcome', [MailPreviewController::class, 'previewWelcome']);
    Route::get('/password-reset', [MailPreviewController::class, 'previewPasswordReset']);
    Route::post('/send', [MailPreviewController::class, 'send']);
});
