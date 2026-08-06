<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Hostel\HostelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hostel Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('hostel')->middleware(['auth:sanctum'])->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [HostelController::class, 'getDashboard'])->name('hostel.dashboard');

    // ===================== BUILDINGS =====================
    Route::prefix('buildings')->group(function () {
        Route::get('/', [HostelController::class, 'index'])->name('hostel.buildings');
        Route::post('/', [HostelController::class, 'store'])->name('hostel.buildings.store');
        Route::get('/{uuid}', [HostelController::class, 'show'])->name('hostel.buildings.show');
    });

    // ===================== ROOMS =====================
    Route::prefix('rooms')->group(function () {
        Route::get('/', [HostelController::class, 'getRooms'])->name('hostel.rooms');
    });

    // ===================== ADMISSIONS =====================
    Route::prefix('admissions')->group(function () {
        Route::get('/', [HostelController::class, 'getAdmissions'])->name('hostel.admissions');
        Route::post('/', [HostelController::class, 'admitStudent'])->name('hostel.admissions.store');
        Route::post('/{uuid}/check-in', [HostelController::class, 'checkInStudent'])->name('hostel.admissions.checkin');
    });

    // ===================== VISITORS =====================
    Route::prefix('visitors')->group(function () {
        Route::get('/', [HostelController::class, 'getVisitors'])->name('hostel.visitors');
        Route::post('/', [HostelController::class, 'registerVisitor'])->name('hostel.visitors.store');
    });
});
