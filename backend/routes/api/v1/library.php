<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Library\BookController;
use App\Http\Controllers\Api\V1\Library\IssueController;
use App\Http\Controllers\Api\V1\Library\MemberController;
use App\Http\Controllers\Api\V1\Library\FineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Library Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('library')->middleware(['auth:sanctum'])->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [BookController::class, 'getStats'])->name('library.dashboard');

    // ===================== BOOKS =====================
    Route::prefix('books')->group(function () {
        Route::get('/', [BookController::class, 'index'])->name('library.books');
        Route::post('/', [BookController::class, 'store'])->name('library.books.store');
        Route::get('/categories', [BookController::class, 'getCategories'])->name('library.books.categories');
        Route::get('/stats', [BookController::class, 'getStats'])->name('library.books.stats');
        Route::get('/search', [BookController::class, 'search'])->name('library.books.search');
        Route::get('/{uuid}', [BookController::class, 'show'])->name('library.books.show');
        Route::post('/{uuid}/copies', [BookController::class, 'addCopy'])->name('library.books.copies');
    });

    // ===================== ISSUES =====================
    Route::prefix('issues')->group(function () {
        Route::get('/', [IssueController::class, 'index'])->name('library.issues');
        Route::post('/', [IssueController::class, 'store'])->name('library.issues.store');
        Route::get('/today-stats', [IssueController::class, 'getTodayStats'])->name('library.issues.today');
        Route::get('/{uuid}', [IssueController::class, 'show'])->name('library.issues.show');
        Route::post('/{uuid}/return', [IssueController::class, 'return'])->name('library.issues.return');
        Route::post('/{uuid}/renew', [IssueController::class, 'renew'])->name('library.issues.renew');
    });

    // ===================== RESERVATIONS =====================
    Route::prefix('reservations')->group(function () {
        Route::post('/', [IssueController::class, 'reserve'])->name('library.reservations.store');
    });

    // ===================== MEMBERS =====================
    Route::prefix('members')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('library.members');
        Route::post('/', [MemberController::class, 'store'])->name('library.members.store');
        Route::get('/types', [MemberController::class, 'getMemberTypes'])->name('library.members.types');
        Route::get('/{uuid}', [MemberController::class, 'show'])->name('library.members.show');
    });

    // ===================== FINES =====================
    Route::prefix('fines')->group(function () {
        Route::get('/', [FineController::class, 'index'])->name('library.fines');
        Route::post('/', [FineController::class, 'store'])->name('library.fines.store');
        Route::get('/stats', [FineController::class, 'getStats'])->name('library.fines.stats');
        Route::get('/{uuid}', [FineController::class, 'show'])->name('library.fines.show');
        Route::post('/{uuid}/collect', [FineController::class, 'collect'])->name('library.fines.collect');
        Route::post('/{uuid}/waive', [FineController::class, 'waive'])->name('library.fines.waive');
    });
});
