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

// API v1 routes
Route::prefix('v1')->group(function () {
    require __DIR__ . '/v1/auth.php';
    require __DIR__ . '/v1/user.php';
    require __DIR__ . '/v1/academic.php';
    require __DIR__ . '/v1/admission.php';
    require __DIR__ . '/v1/student.php';
    require __DIR__ . '/v1/teacher.php';
    require __DIR__ . '/v1/employee.php';
    require __DIR__ . '/v1/attendance.php';
    require __DIR__ . '/v1/exam.php';
    require __DIR__ . '/v1/result.php';
    require __DIR__ . '/v1/finance.php';
    require __DIR__ . '/v1/payment.php';
    require __DIR__ . '/v1/hostel.php';
    require __DIR__ . '/v1/transport.php';
    require __DIR__ . '/v1/library.php';
    require __DIR__ . '/v1/inventory.php';
    require __DIR__ . '/v1/routine.php';
    require __DIR__ . '/v1/hr.php';
    require __DIR__ . '/v1/crm.php';
    require __DIR__ . '/v1/certificate.php';
    require __DIR__ . '/v1/document.php';
    require __DIR__ . '/v1/facility.php';
    require __DIR__ . '/v1/report.php';
    require __DIR__ . '/v1/settings.php';
    require __DIR__ . '/v1/cms.php';
    require __DIR__ . '/v1/convocation.php';
});

// API v3 routes (DevSecOps Platform)
Route::prefix('v3')->group(function () {
    require __DIR__ . '/v3/devsecops.php';
});
