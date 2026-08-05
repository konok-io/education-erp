<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Employee\EmployeeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Employee Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('employees')->middleware(['auth:sanctum'])->group(function () {

    // CRUD
    Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/search', [EmployeeController::class, 'search'])->name('employees.search');
    Route::get('/{uuid}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::put('/{uuid}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/{uuid}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Profile
    Route::post('/{uuid}/profile', [EmployeeController::class, 'updateProfile'])->name('employees.profile.update');
    Route::post('/{uuid}/photo', [EmployeeController::class, 'updatePhoto'])->name('employees.photo.update');

    // Status
    Route::post('/{uuid}/status', [EmployeeController::class, 'updateStatus'])->name('employees.status.update');

    // Salary
    Route::post('/{uuid}/salary', [EmployeeController::class, 'updateSalary'])->name('employees.salary.update');

    // Leave
    Route::get('/{uuid}/leaves', [EmployeeController::class, 'getLeaveHistory'])->name('employees.leaves');
    Route::post('/{uuid}/leaves', [EmployeeController::class, 'applyLeave'])->name('employees.leaves.apply');

    // QR Code
    Route::get('/{uuid}/qr-code', [EmployeeController::class, 'generateQRCode'])->name('employees.qr-code');

    // Import/Export
    Route::post('/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/export', [EmployeeController::class, 'export'])->name('employees.export');

    // Statistics
    Route::get('/statistics', [EmployeeController::class, 'statistics'])->name('employees.statistics');
    Route::get('/active-count', [EmployeeController::class, 'activeCount'])->name('employees.active-count');

    // Lookups
    Route::get('/lookups/departments', [EmployeeController::class, 'getDepartments'])->name('employees.lookups.departments');
    Route::get('/lookups/designations', [EmployeeController::class, 'getDesignations'])->name('employees.lookups.designations');
    Route::get('/lookups/shifts', [EmployeeController::class, 'getShifts'])->name('employees.lookups.shifts');
    Route::get('/lookups/salary-grades', [EmployeeController::class, 'getSalaryGrades'])->name('employees.lookups.salary-grades');
});
