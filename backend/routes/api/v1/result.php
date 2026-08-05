<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Result\ResultController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Result & Examination Routes
|--------------------------------------------------------------------------
*/

Route::prefix('results')->middleware(['auth:sanctum'])->group(function () {

    // Exams
    Route::get('/exams', [ResultController::class, 'getExams'])->name('results.exams');
    Route::post('/exams', [ResultController::class, 'createExam'])->name('results.exams.create');
    Route::put('/exams/{uuid}', [ResultController::class, 'updateExam'])->name('results.exams.update');
    Route::delete('/exams/{uuid}', [ResultController::class, 'deleteExam'])->name('results.exams.delete');

    // Mark Entry
    Route::post('/marks', [ResultController::class, 'entryMarks'])->name('results.marks.entry');
    Route::put('/marks/{uuid}', [ResultController::class, 'updateMarks'])->name('results.marks.update');

    // Student Results
    Route::get('/student', [ResultController::class, 'getStudentResults'])->name('results.student');

    // Result Processing
    Route::post('/process', [ResultController::class, 'processResults'])->name('results.process');
    Route::get('/class', [ResultController::class, 'getClassResults'])->name('results.class');

    // GPA/CGPA
    Route::get('/gpa', [ResultController::class, 'calculateGPA'])->name('results.gpa');
    Route::get('/cgpa', [ResultController::class, 'calculateCGPA'])->name('results.cgpa');

    // Publish/Approve
    Route::post('/{uuid}/verify', [ResultController::class, 'verifyResult'])->name('results.verify');
    Route::post('/{uuid}/approve', [ResultController::class, 'approveResult'])->name('results.approve');
    Route::post('/publish', [ResultController::class, 'publishResult'])->name('results.publish');
    Route::post('/{uuid}/lock', [ResultController::class, 'lockResult'])->name('results.lock');

    // Transcript/Marksheet
    Route::get('/transcript/{studentId}', [ResultController::class, 'getTranscript'])->name('results.transcript');
    Route::get('/marksheet', [ResultController::class, 'getMarksheet'])->name('results.marksheet');

    // Merit List
    Route::get('/merit-list', [ResultController::class, 'getMeritList'])->name('results.merit-list');
    Route::get('/fail-list', [ResultController::class, 'getFailList'])->name('results.fail-list');

    // Analytics
    Route::get('/analytics', [ResultController::class, 'getAnalytics'])->name('results.analytics');
    Route::get('/subject-analysis', [ResultController::class, 'getSubjectAnalysis'])->name('results.subject-analysis');

    // Re-scrutiny
    Route::post('/rescrutiny', [ResultController::class, 'applyReScrutiny'])->name('results.rescrutiny.apply');
    Route::put('/rescrutiny/{uuid}', [ResultController::class, 'reviewReScrutiny'])->name('results.rescrutiny.review');

    // Grade Rules
    Route::get('/grade-rules', [ResultController::class, 'getGradeRules'])->name('results.grade-rules');
    Route::post('/grade-rules', [ResultController::class, 'createGradeRule'])->name('results.grade-rules.create');

    // Export
    Route::get('/export', [ResultController::class, 'exportResults'])->name('results.export');
});