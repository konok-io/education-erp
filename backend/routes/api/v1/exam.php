<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Exam\ExamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Examination Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('exam')->middleware(['auth:sanctum'])->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [ExamController::class, 'getDashboard'])->name('exam.dashboard');

    // ===================== EXAM SESSIONS =====================
    Route::prefix('sessions')->group(function () {
        Route::get('/', [ExamController::class, 'getSessions'])->name('exam.sessions');
        Route::post('/', [ExamController::class, 'createSession'])->name('exam.sessions.store');
    });

    // ===================== EXAM CENTERS =====================
    Route::prefix('centers')->group(function () {
        Route::get('/', [ExamController::class, 'getCenters'])->name('exam.centers');
        Route::post('/', [ExamController::class, 'createCenter'])->name('exam.centers.store');
    });

    // ===================== EXAMS =====================
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'getExams'])->name('exam.exams');
        Route::post('/', [ExamController::class, 'createExam'])->name('exam.exams.store');
        Route::get('/{uuid}', [ExamController::class, 'showExam'])->name('exam.exams.show');
        Route::post('/{uuid}/publish', [ExamController::class, 'publishExam'])->name('exam.exams.publish');
        Route::post('/{uuid}/evaluate', [ExamController::class, 'evaluateExam'])->name('exam.exams.evaluate');
    });

    // ===================== QUESTION BANK =====================
    Route::prefix('questions')->group(function () {
        Route::get('/', [ExamController::class, 'getQuestions'])->name('exam.questions');
        Route::post('/', [ExamController::class, 'createQuestion'])->name('exam.questions.store');
        Route::get('/categories', [ExamController::class, 'getQuestionCategories'])->name('exam.questions.categories');
        Route::get('/{uuid}', [ExamController::class, 'showQuestion'])->name('exam.questions.show');
    });

    // ===================== SEAT PLANS =====================
    Route::prefix('seat-plans')->group(function () {
        Route::get('/', [ExamController::class, 'getSeatPlans'])->name('exam.seat-plans');
        Route::post('/', [ExamController::class, 'generateSeatPlan'])->name('exam.seat-plans.store');
    });

    // ===================== ADMIT CARDS =====================
    Route::prefix('admit-cards')->group(function () {
        Route::get('/', [ExamController::class, 'getAdmitCards'])->name('exam.admit-cards');
        Route::post('/', [ExamController::class, 'generateAdmitCard'])->name('exam.admit-cards.store');
    });

    // ===================== RESULTS =====================
    Route::prefix('results')->group(function () {
        Route::get('/', [ExamController::class, 'getResults'])->name('exam.results');
    });

    // ===================== CBT =====================
    Route::prefix('cbt')->group(function () {
        Route::post('/start', [ExamController::class, 'startCbtSession'])->name('exam.cbt.start');
        Route::post('/{uuid}/answer', [ExamController::class, 'submitAnswer'])->name('exam.cbt.answer');
    });
});
