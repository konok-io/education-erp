<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Facility\FacilityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Facility Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('facility')->middleware(['auth:sanctum'])->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [FacilityController::class, 'getDashboard'])->name('facility.dashboard');

    // ===================== FACILITY TYPES =====================
    Route::get('/types', [FacilityController::class, 'getFacilityTypes'])->name('facility.types');

    // ===================== FACILITIES =====================
    Route::prefix('facilities')->group(function () {
        Route::get('/', [FacilityController::class, 'index'])->name('facility.facilities');
        Route::post('/', [FacilityController::class, 'store'])->name('facility.facilities.store');
        Route::get('/{uuid}', [FacilityController::class, 'show'])->name('facility.facilities.show');
    });

    // ===================== BOOKINGS =====================
    Route::prefix('bookings')->group(function () {
        Route::get('/', [FacilityController::class, 'getBookings'])->name('facility.bookings');
        Route::post('/', [FacilityController::class, 'createBooking'])->name('facility.bookings.store');
        Route::post('/{uuid}/approve', [FacilityController::class, 'approveBooking'])->name('facility.bookings.approve');
        Route::post('/{uuid}/reject', [FacilityController::class, 'rejectBooking'])->name('facility.bookings.reject');
    });

    // ===================== MAINTENANCE =====================
    Route::prefix('maintenance')->group(function () {
        Route::get('/', [FacilityController::class, 'getMaintenanceRequests'])->name('facility.maintenance');
        Route::post('/', [FacilityController::class, 'createMaintenanceRequest'])->name('facility.maintenance.store');
        Route::post('/{uuid}/assign', [FacilityController::class, 'assignMaintenance'])->name('facility.maintenance.assign');
        Route::post('/{uuid}/complete', [FacilityController::class, 'completeMaintenance'])->name('facility.maintenance.complete');
    });
});
