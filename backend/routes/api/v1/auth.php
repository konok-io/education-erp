<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    // Login
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Password reset
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');
});

// Protected routes
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Refresh token
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

    // Current user
    Route::get('/me', [AuthController::class, 'me'])->name('auth.me');

    // Change password
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');

    // Login history
    Route::get('/history', [AuthController::class, 'loginHistory'])->name('auth.history');

    // Session management
    Route::get('/sessions', [AuthController::class, 'activeSessions'])->name('auth.sessions');
    Route::delete('/sessions/{sessionId}', [AuthController::class, 'logoutSession'])->name('auth.logout-session');
    Route::delete('/sessions', [AuthController::class, 'logoutAllDevices'])->name('auth.logout-all');
});