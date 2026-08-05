<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admission\AdmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admission Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admissions')->middleware(['auth:sanctum'])->group(function () {

    // Campaigns
    Route::get('/campaigns', [AdmissionController::class, 'getCampaigns'])->name('admissions.campaigns');
    Route::post('/campaigns', [AdmissionController::class, 'createCampaign'])->name('admissions.campaigns.create');
    Route::put('/campaigns/{uuid}', [AdmissionController::class, 'updateCampaign'])->name('admissions.campaigns.update');
    Route::post('/campaigns/{uuid}/toggle', [AdmissionController::class, 'toggleCampaign'])->name('admissions.campaigns.toggle');

    // Applications
    Route::get('/', [AdmissionController::class, 'getApplications'])->name('admissions.index');
    Route::post('/', [AdmissionController::class, 'createApplication'])->name('admissions.store');
    Route::get('/{uuid}', [AdmissionController::class, 'getApplication'])->name('admissions.show');
    Route::put('/{uuid}', [AdmissionController::class, 'updateApplication'])->name('admissions.update');
    Route::post('/{uuid}/submit', [AdmissionController::class, 'submitApplication'])->name('admissions.submit');

    // Documents
    Route::post('/documents', [AdmissionController::class, 'uploadDocument'])->name('admissions.documents.upload');
    Route::put('/documents/{uuid}/verify', [AdmissionController::class, 'verifyDocument'])->name('admissions.documents.verify');

    // Payments
    Route::post('/payment', [AdmissionController::class, 'initiatePayment'])->name('admissions.payment.initiate');
    Route::put('/payment/{uuid}/verify', [AdmissionController::class, 'verifyPayment'])->name('admissions.payment.verify');

    // Merit & Approval
    Route::post('/merit', [AdmissionController::class, 'generateMeritList'])->name('admissions.merit.generate');
    Route::put('/{uuid}/merit', [AdmissionController::class, 'updateMeritPosition'])->name('admissions.merit.update');
    Route::post('/{uuid}/approve', [AdmissionController::class, 'approveApplication'])->name('admissions.approve');
    Route::post('/{uuid}/reject', [AdmissionController::class, 'rejectApplication'])->name('admissions.reject');

    // Interview
    Route::post('/{uuid}/interview', [AdmissionController::class, 'scheduleInterview'])->name('admissions.interview.schedule');

    // Dashboard
    Route::get('/dashboard/stats', [AdmissionController::class, 'getDashboard'])->name('admissions.dashboard');
    Route::get('/dashboard/applicant/{applicationNo}', [AdmissionController::class, 'getApplicantDashboard'])->name('admissions.applicant-dashboard');

    // Reports
    Route::get('/report', [AdmissionController::class, 'getReport'])->name('admissions.report');

    // Export
    Route::get('/export', [AdmissionController::class, 'exportApplications'])->name('admissions.export');
});

// Public route for applicant
Route::get('/admissions/apply/{campaignUuid}', [AdmissionController::class, 'createApplication'])
    ->name('admissions.public.apply');
