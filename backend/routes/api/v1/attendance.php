<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Attendance\AttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Attendance Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('attendance')->middleware(['auth:sanctum'])->group(function () {

    // List
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');

    // Student Attendance
    Route::post('/student', [AttendanceController::class, 'markStudentAttendance'])->name('attendance.student.mark');
    Route::get('/student/{uuid}', [AttendanceController::class, 'getStudentAttendance'])->name('attendance.student');

    // Teacher Attendance
    Route::post('/teacher', [AttendanceController::class, 'markTeacherAttendance'])->name('attendance.teacher.mark');
    Route::get('/teacher/{uuid}', [AttendanceController::class, 'getTeacherAttendance'])->name('attendance.teacher');

    // Employee Attendance
    Route::post('/employee', [AttendanceController::class, 'markEmployeeAttendance'])->name('attendance.employee.mark');
    Route::get('/employee/{uuid}', [AttendanceController::class, 'getEmployeeAttendance'])->name('attendance.employee');

    // QR Attendance
    Route::post('/qr/verify', [AttendanceController::class, 'verifyQRCode'])->name('attendance.qr.verify');
    Route::post('/qr/mark', [AttendanceController::class, 'markByQR'])->name('attendance.qr.mark');

    // Approval
    Route::post('/{uuid}/approve', [AttendanceController::class, 'approve'])->name('attendance.approve');
    Route::post('/approve/bulk', [AttendanceController::class, 'bulkApprove'])->name('attendance.approve.bulk');

    // Correction
    Route::post('/correction', [AttendanceController::class, 'requestCorrection'])->name('attendance.correction.request');
    Route::put('/correction/{uuid}', [AttendanceController::class, 'reviewCorrection'])->name('attendance.correction.review');
    Route::get('/corrections', [AttendanceController::class, 'getCorrectionRequests'])->name('attendance.corrections');

    // Reports
    Route::get('/report', [AttendanceController::class, 'getReport'])->name('attendance.report');
    Route::get('/report/class-summary', [AttendanceController::class, 'getClassAttendanceSummary'])->name('attendance.report.class-summary');

    // Analytics
    Route::get('/analytics', [AttendanceController::class, 'getAnalytics'])->name('attendance.analytics');
    Route::get('/dashboard', [AttendanceController::class, 'getDashboardStats'])->name('attendance.dashboard');

    // Import/Export
    Route::post('/import', [AttendanceController::class, 'import'])->name('attendance.import');
    Route::get('/export', [AttendanceController::class, 'export'])->name('attendance.export');
});