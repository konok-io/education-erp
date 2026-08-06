<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Transport\TransportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Transport Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('transport')->middleware(['auth:sanctum'])->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [TransportController::class, 'getDashboard'])->name('transport.dashboard');

    // ===================== VEHICLES =====================
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [TransportController::class, 'getVehicles'])->name('transport.vehicles');
        Route::post('/', [TransportController::class, 'createVehicle'])->name('transport.vehicles.store');
        Route::get('/{uuid}', [TransportController::class, 'showVehicle'])->name('transport.vehicles.show');
    });

    // ===================== DRIVERS =====================
    Route::prefix('drivers')->group(function () {
        Route::get('/', [TransportController::class, 'getDrivers'])->name('transport.drivers');
        Route::post('/', [TransportController::class, 'createDriver'])->name('transport.drivers.store');
    });

    // ===================== ROUTES =====================
    Route::prefix('routes')->group(function () {
        Route::get('/', [TransportController::class, 'getRoutes'])->name('transport.routes');
        Route::post('/', [TransportController::class, 'createRoute'])->name('transport.routes.store');
    });

    // ===================== ALLOCATIONS =====================
    Route::prefix('allocations')->group(function () {
        Route::get('/', [TransportController::class, 'getAllocations'])->name('transport.allocations');
        Route::post('/', [TransportController::class, 'allocateStudent'])->name('transport.allocations.store');
    });
});
