<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Student\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('students')->middleware(['auth:sanctum'])->group(function () {

    // CRUD
    Route::get('/', [StudentController::class, 'index'])->name('students.index');
    Route::post('/', [StudentController::class, 'store'])->name('students.store');
    Route::get('/search', [StudentController::class, 'search'])->name('students.search');
    Route::get('/by-number/{studentNo}', [StudentController::class, 'findByStudentNo'])->name('students.by-number');
    Route::get('/{uuid}', [StudentController::class, 'show'])->name('students.show');
    Route::put('/{uuid}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/{uuid}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Profile
    Route::post('/{uuid}/profile', [StudentController::class, 'updateProfile'])->name('students.profile.update');
    Route::post('/{uuid}/photo', [StudentController::class, 'updatePhoto'])->name('students.photo.update');

    // Guardian
    Route::post('/{uuid}/guardian', [StudentController::class, 'updateGuardian'])->name('students.guardian.update');

    // Medical
    Route::post('/{uuid}/medical', [StudentController::class, 'updateMedical'])->name('students.medical.update');

    // Documents
    Route::get('/{uuid}/documents', [StudentController::class, 'getDocuments'])->name('students.documents');
    Route::post('/{uuid}/documents', [StudentController::class, 'uploadDocument'])->name('students.documents.upload');
    Route::delete('/{uuid}/documents/{documentUuid}', [StudentController::class, 'deleteDocument'])->name('students.documents.delete');

    // Status
    Route::post('/{uuid}/status', [StudentController::class, 'updateStatus'])->name('students.status.update');

    // Promotion
    Route::post('/{uuid}/promote', [StudentController::class, 'promote'])->name('students.promote');
    Route::get('/{uuid}/promotions', [StudentController::class, 'promotionHistory'])->name('students.promotions');

    // Transfer
    Route::post('/{uuid}/transfer', [StudentController::class, 'transfer'])->name('students.transfer');
    Route::get('/{uuid}/transfers', [StudentController::class, 'transferHistory'])->name('students.transfers');

    // QR Code
    Route::get('/{uuid}/qr-code', [StudentController::class, 'generateQRCode'])->name('students.qr-code');

    // Import/Export
    Route::post('/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/export', [StudentController::class, 'export'])->name('students.export');

    // Statistics
    Route::get('/statistics', [StudentController::class, 'statistics'])->name('students.statistics');
    Route::get('/active-count', [StudentController::class, 'activeCount'])->name('students.active-count');
});