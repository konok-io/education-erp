<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Certificate\CertificateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Certificate, Transcript & Verification Routes
|--------------------------------------------------------------------------
*/

// Public verification routes
Route::get('/verify/{code}', [CertificateController::class, 'publicVerify'])->name('certificate.verify.public');

Route::middleware(['auth:sanctum'])->prefix('certificate')->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [CertificateController::class, 'getDashboard'])->name('certificate.dashboard');

    // ===================== REQUESTS =====================
    Route::prefix('requests')->group(function () {
        Route::get('/', [CertificateController::class, 'getRequests'])->name('certificate.requests');
        Route::post('/', [CertificateController::class, 'createRequest'])->name('certificate.requests.create');
        Route::get('/{uuid}', [CertificateController::class, 'showRequest'])->name('certificate.requests.show');
        Route::post('/{uuid}/approve', [CertificateController::class, 'approveRequest'])->name('certificate.requests.approve');
        Route::post('/{uuid}/reject', [CertificateController::class, 'rejectRequest'])->name('certificate.requests.reject');
    });

    // ===================== CERTIFICATES =====================
    Route::prefix('certificates')->group(function () {
        Route::get('/', [CertificateController::class, 'getCertificates'])->name('certificate.certificates');
        Route::post('/', [CertificateController::class, 'generateCertificate'])->name('certificate.certificates.generate');
        Route::get('/{uuid}', [CertificateController::class, 'showCertificate'])->name('certificate.certificates.show');
        Route::post('/{uuid}/approve', [CertificateController::class, 'approveCertificate'])->name('certificate.certificates.approve');
        Route::post('/{uuid}/issue', [CertificateController::class, 'issueCertificate'])->name('certificate.certificates.issue');
        Route::post('/{uuid}/cancel', [CertificateController::class, 'cancelCertificate'])->name('certificate.certificates.cancel');
    });

    // ===================== TEMPLATES =====================
    Route::prefix('templates')->group(function () {
        Route::get('/', [CertificateController::class, 'getTemplates'])->name('certificate.templates');
        Route::post('/', [CertificateController::class, 'createTemplate'])->name('certificate.templates.create');
        Route::get('/{uuid}', [CertificateController::class, 'showTemplate'])->name('certificate.templates.show');
        Route::put('/{uuid}', [CertificateController::class, 'updateTemplate'])->name('certificate.templates.update');
    });

    // ===================== TRANSCRIPTS =====================
    Route::prefix('transcripts')->group(function () {
        Route::get('/', [CertificateController::class, 'getTranscripts'])->name('certificate.transcripts');
        Route::post('/', [CertificateController::class, 'createTranscript'])->name('certificate.transcripts.create');
        Route::get('/{uuid}', [CertificateController::class, 'showTranscript'])->name('certificate.transcripts.show');
        Route::post('/{uuid}/verify', [CertificateController::class, 'verifyTranscript'])->name('certificate.transcripts.verify');
        Route::post('/{uuid}/approve', [CertificateController::class, 'approveTranscript'])->name('certificate.transcripts.approve');
        Route::post('/{uuid}/issue', [CertificateController::class, 'issueTranscript'])->name('certificate.transcripts.issue');
    });

    // ===================== DIGITAL SIGNATURES =====================
    Route::prefix('signatures')->group(function () {
        Route::get('/', [CertificateController::class, 'getSignatures'])->name('certificate.signatures');
        Route::post('/', [CertificateController::class, 'createSignature'])->name('certificate.signatures.create');
        Route::get('/{uuid}', [CertificateController::class, 'showSignature'])->name('certificate.signatures.show');
        Route::put('/{uuid}', [CertificateController::class, 'updateSignature'])->name('certificate.signatures.update');
        Route::get('/active', [CertificateController::class, 'getActiveSignatures'])->name('certificate.signatures.active');
    });

    // ===================== DUPLICATES =====================
    Route::prefix('duplicates')->group(function () {
        Route::get('/', [CertificateController::class, 'getDuplicateRequests'])->name('certificate.duplicates');
        Route::post('/', [CertificateController::class, 'requestDuplicate'])->name('certificate.duplicates.request');
        Route::post('/{uuid}/approve', [CertificateController::class, 'approveDuplicate'])->name('certificate.duplicates.approve');
    });

    // ===================== REPORTS =====================
    Route::prefix('reports')->group(function () {
        Route::get('/issued', [CertificateController::class, 'getIssuedReport'])->name('certificate.reports.issued');
        Route::get('/pending', [CertificateController::class, 'getPendingReport'])->name('certificate.reports.pending');
        Route::get('/verification', [CertificateController::class, 'getVerificationReport'])->name('certificate.reports.verification');
    });
});
