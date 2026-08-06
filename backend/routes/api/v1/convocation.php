<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Convocation\ConvocationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Convocation Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('convocation')->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [ConvocationController::class, 'getDashboard'])->name('convocation.dashboard');

    // ===================== CONVOCATIONS =====================
    Route::prefix('convocations')->group(function () {
        Route::get('/', [ConvocationController::class, 'getConvocations'])->name('convocation.convocations');
        Route::post('/', [ConvocationController::class, 'createConvocation'])->name('convocation.convocations.store');
        Route::get('/{uuid}', [ConvocationController::class, 'showConvocation'])->name('convocation.convocations.show');
        Route::put('/{uuid}', [ConvocationController::class, 'updateConvocation'])->name('convocation.convocations.update');
        Route::post('/{uuid}/open-registration', [ConvocationController::class, 'openRegistration'])->name('convocation.convocations.open-registration');
        Route::post('/{uuid}/close-registration', [ConvocationController::class, 'closeRegistration'])->name('convocation.convocations.close-registration');
    });

    // ===================== REGISTRATIONS =====================
    Route::prefix('registrations')->group(function () {
        Route::get('/', [ConvocationController::class, 'getRegistrations'])->name('convocation.registrations');
        Route::post('/', [ConvocationController::class, 'registerAlumni'])->name('convocation.registrations.store');
        Route::post('/{uuid}/confirm', [ConvocationController::class, 'confirmRegistration'])->name('convocation.registrations.confirm');
        Route::post('/{uuid}/attendance', [ConvocationController::class, 'markAttendance'])->name('convocation.registrations.attendance');
    });
});
