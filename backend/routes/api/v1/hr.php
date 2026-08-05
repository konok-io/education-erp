<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\HR\HRController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HR, Payroll & Leave Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('hr')->middleware(['auth:sanctum'])->group(function () {

    // Salary Grades
    Route::get('/salary-grades', [HRController::class, 'getSalaryGrades'])->name('hr.salary-grades');
    Route::post('/salary-grades', [HRController::class, 'createSalaryGrade'])->name('hr.salary-grades.create');

    // Payroll
    Route::get('/payrolls', [HRController::class, 'getPayrolls'])->name('hr.payrolls');
    Route::post('/payrolls', [HRController::class, 'processPayroll'])->name('hr.payrolls.process');
    Route::post('/payrolls/bulk', [HRController::class, 'processBulkPayroll'])->name('hr.payrolls.bulk');
    Route::post('/payrolls/{uuid}/approve', [HRController::class, 'approvePayroll'])->name('hr.payrolls.approve');
    Route::post('/payrolls/{uuid}/pay', [HRController::class, 'payPayroll'])->name('hr.payrolls.pay');
    Route::get('/payrolls/{uuid}/payslip', [HRController::class, 'getPayslip'])->name('hr.payrolls.payslip');

    // Leave Types
    Route::get('/leave-types', [HRController::class, 'getLeaveTypes'])->name('hr.leave-types');
    Route::post('/leave-types', [HRController::class, 'createLeaveType'])->name('hr.leave-types.create');

    // Leaves
    Route::get('/leaves', [HRController::class, 'getLeaves'])->name('hr.leaves');
    Route::post('/leaves', [HRController::class, 'applyLeave'])->name('hr.leaves.apply');
    Route::post('/leaves/{uuid}/approve', [HRController::class, 'approveLeave'])->name('hr.leaves.approve');
    Route::post('/leaves/{uuid}/reject', [HRController::class, 'rejectLeave'])->name('hr.leaves.reject');
    Route::get('/leaves/balance/{employeeId}', [HRController::class, 'getLeaveBalance'])->name('hr.leaves.balance');

    // Holidays
    Route::get('/holidays', [HRController::class, 'getHolidays'])->name('hr.holidays');
    Route::post('/holidays', [HRController::class, 'createHoliday'])->name('hr.holidays.create');

    // Loans
    Route::get('/loans', [HRController::class, 'getLoans'])->name('hr.loans');
    Route::post('/loans', [HRController::class, 'createLoan'])->name('hr.loans.create');
    Route::post('/loans/{uuid}/approve', [HRController::class, 'approveLoan'])->name('hr.loans.approve');
    Route::get('/loans/balance/{employeeId}', [HRController::class, 'getLoanBalance'])->name('hr.loans.balance');

    // Overtime
    Route::get('/overtimes', [HRController::class, 'getOvertimes'])->name('hr.overtimes');
    Route::post('/overtimes', [HRController::class, 'createOvertime'])->name('hr.overtimes.create');
    Route::post('/overtimes/{uuid}/approve', [HRController::class, 'approveOvertime'])->name('hr.overtimes.approve');

    // Reports
    Route::get('/reports/payroll', [HRController::class, 'getPayrollReport'])->name('hr.reports.payroll');
    Route::get('/reports/leave', [HRController::class, 'getLeaveReport'])->name('hr.reports.leave');

    // Dashboard
    Route::get('/dashboard', [HRController::class, 'getDashboard'])->name('hr.dashboard');

    // Export
    Route::get('/export/payslips', [HRController::class, 'exportPayslips'])->name('hr.export.payslips');
});
