<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Education ERP REST API Routes
| All API routes are prefixed with /api
|
*/

// Health check endpoint
Route::get('/health', [Api\HealthController::class, 'check']);

// API v1 routes (ready for future expansion)
Route::prefix('v1')->group(function () {
    // Routes will be added in Phase 003 onwards
});
