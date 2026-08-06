<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V3\Identity\AuthController;
use App\Http\Controllers\Api\V3\Identity\MFAController;
use App\Http\Controllers\Api\V3\Identity\SessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Identity & Access Management API Routes (v3)
|--------------------------------------------------------------------------
|
| Routes for authentication, sessions, MFA, and identity management
|
*/

// Public Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/mfa', [AuthController::class, 'loginWithMFA']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Protected Authentication Routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Session Management
Route::middleware('auth:sanctum')->prefix('sessions')->group(function () {
    Route::get('/', [SessionController::class, 'index']);
    Route::get('/{id}', [SessionController::class, 'show']);
    Route::delete('/{id}', [SessionController::class, 'destroy']);
    Route::delete('/', [SessionController::class, 'destroyAll']);
});

// MFA Management
Route::middleware('auth:sanctum')->prefix('mfa')->group(function () {
    Route::get('/', [MFAController::class, 'index']);
    Route::post('/totp/setup', [MFAController::class, 'setupTOTP']);
    Route::post('/sms/setup', [MFAController::class, 'setupSMS']);
    Route::post('/verify-setup', [MFAController::class, 'verifySetup']);
    Route::post('/verify', [MFAController::class, 'verify']);
    Route::post('/backup-codes', [MFAController::class, 'generateBackupCodes']);
    Route::delete('/{id}', [MFAController::class, 'destroy']);
});

// Identity & Users
Route::middleware('auth:sanctum')->prefix('identity')->group(function () {
    Route::get('/users', [AuthController::class, 'me']);
    Route::get('/sessions', [SessionController::class, 'index']);
});
