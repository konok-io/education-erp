<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Teacher\TeacherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Teacher Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('teachers')->middleware(['auth:sanctum'])->group(function () {

    // CRUD
    Route::get('/', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/search', [TeacherController::class, 'search'])->name('teachers.search');
    Route::get('/by-number/{teacherNo}', [TeacherController::class, 'findByTeacherNo'])->name('teachers.by-number');
    Route::get('/{uuid}', [TeacherController::class, 'show'])->name('teachers.show');
    Route::put('/{uuid}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/{uuid}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

    // Profile
    Route::post('/{uuid}/profile', [TeacherController::class, 'updateProfile'])->name('teachers.profile.update');
    Route::post('/{uuid}/photo', [TeacherController::class, 'updatePhoto'])->name('teachers.photo.update');

    // Qualifications
    Route::post('/{uuid}/qualifications', [TeacherController::class, 'addQualification'])->name('teachers.qualifications.store');
    Route::put('/{uuid}/qualifications/{qualUuid}', [TeacherController::class, 'updateQualification'])->name('teachers.qualifications.update');
    Route::delete('/{uuid}/qualifications/{qualUuid}', [TeacherController::class, 'deleteQualification'])->name('teachers.qualifications.destroy');

    // Experiences
    Route::post('/{uuid}/experiences', [TeacherController::class, 'addExperience'])->name('teachers.experiences.store');
    Route::put('/{uuid}/experiences/{expUuid}', [TeacherController::class, 'updateExperience'])->name('teachers.experiences.update');
    Route::delete('/{uuid}/experiences/{expUuid}', [TeacherController::class, 'deleteExperience'])->name('teachers.experiences.destroy');

    // Subject Assignment
    Route::get('/{uuid}/subjects', [TeacherController::class, 'getAssignedSubjects'])->name('teachers.subjects');
    Route::post('/{uuid}/subjects', [TeacherController::class, 'assignSubjects'])->name('teachers.subjects.assign');
    Route::delete('/{uuid}/subjects/{assignmentUuid}', [TeacherController::class, 'removeSubject'])->name('teachers.subjects.remove');

    // Class Assignment
    Route::get('/{uuid}/classes', [TeacherController::class, 'getAssignedClasses'])->name('teachers.classes');
    Route::post('/{uuid}/classes', [TeacherController::class, 'assignClasses'])->name('teachers.classes.assign');
    Route::delete('/{uuid}/classes/{assignmentUuid}', [TeacherController::class, 'removeClass'])->name('teachers.classes.remove');

    // Salary
    Route::get('/{uuid}/salary', [TeacherController::class, 'getSalary'])->name('teachers.salary');
    Route::post('/{uuid}/salary', [TeacherController::class, 'updateSalary'])->name('teachers.salary.update');

    // Leave
    Route::get('/{uuid}/leaves', [TeacherController::class, 'getLeaveHistory'])->name('teachers.leaves');
    Route::post('/{uuid}/leaves', [TeacherController::class, 'applyLeave'])->name('teachers.leaves.apply');

    // Status
    Route::post('/{uuid}/status', [TeacherController::class, 'updateStatus'])->name('teachers.status.update');

    // QR Code
    Route::get('/{uuid}/qr-code', [TeacherController::class, 'generateQRCode'])->name('teachers.qr-code');

    // Import/Export
    Route::post('/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::get('/export', [TeacherController::class, 'export'])->name('teachers.export');

    // Statistics
    Route::get('/statistics', [TeacherController::class, 'statistics'])->name('teachers.statistics');
    Route::get('/active-count', [TeacherController::class, 'activeCount'])->name('teachers.active-count');
});