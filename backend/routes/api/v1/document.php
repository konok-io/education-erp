<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Document\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Document Verification Routes
|--------------------------------------------------------------------------
*/

// Public verification route (no auth required)
Route::get('/verify/{code}', [DocumentController::class, 'publicVerify'])->name('document.public.verify');

Route::prefix('document')->middleware(['auth:sanctum'])->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [DocumentController::class, 'getDashboard'])->name('document.dashboard');

    // ===================== VERIFICATIONS =====================
    Route::prefix('verifications')->group(function () {
        Route::get('/', [DocumentController::class, 'getVerifications'])->name('document.verifications');
        Route::post('/', [DocumentController::class, 'createVerification'])->name('document.verifications.store');
        Route::get('/{uuid}', [DocumentController::class, 'showVerification'])->name('document.verifications.show');
        Route::post('/{uuid}/verify', [DocumentController::class, 'verifyDocument'])->name('document.verifications.verify');
        Route::post('/{uuid}/reject', [DocumentController::class, 'rejectVerification'])->name('document.verifications.reject');
    });
});
