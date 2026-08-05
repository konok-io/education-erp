<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Routine\RoutineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routine Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('routines')->middleware(['auth:sanctum'])->group(function () {

    // Routine CRUD
    Route::get('/', [RoutineController::class, 'index'])->name('routines.index');
    Route::post('/', [RoutineController::class, 'store'])->name('routines.store');
    Route::get('/{uuid}', [RoutineController::class, 'show'])->name('routines.show');
    Route::put('/{uuid}', [RoutineController::class, 'update'])->name('routines.update');
    Route::delete('/{uuid}', [RoutineController::class, 'destroy'])->name('routines.destroy');

    // Bulk Operations
    Route::post('/bulk', [RoutineController::class, 'bulkCreate'])->name('routines.bulk');

    // Publish
    Route::post('/publish', [RoutineController::class, 'publish'])->name('routines.publish');

    // Generator
    Route::post('/generate', [RoutineController::class, 'generate'])->name('routines.generate');

    // Conflict Detection
    Route::post('/conflicts', [RoutineController::class, 'checkConflicts'])->name('routines.conflicts');

    // Teacher/Student/Class Routine
    Route::get('/teacher/{uuid}', [RoutineController::class, 'teacherRoutine'])->name('routines.teacher');
    Route::get('/student/{uuid}', [RoutineController::class, 'studentRoutine'])->name('routines.student');
    Route::get('/class', [RoutineController::class, 'classRoutine'])->name('routines.class');

    // Time Slots
    Route::get('/time-slots', [RoutineController::class, 'getTimeSlots'])->name('routines.time-slots');
    Route::post('/time-slots', [RoutineController::class, 'createTimeSlot'])->name('routines.time-slots.create');

    // Rooms
    Route::get('/rooms', [RoutineController::class, 'getRooms'])->name('routines.rooms');
    Route::post('/rooms', [RoutineController::class, 'createRoom'])->name('routines.rooms.create');

    // Calendar
    Route::get('/calendar', [RoutineController::class, 'getCalendar'])->name('routines.calendar');
    Route::post('/calendar', [RoutineController::class, 'createCalendarEvent'])->name('routines.calendar.create');

    // Holidays
    Route::get('/holidays', [RoutineController::class, 'getHolidays'])->name('routines.holidays');
    Route::post('/holidays', [RoutineController::class, 'createHoliday'])->name('routines.holidays.create');
    Route::delete('/holidays/{uuid}', [RoutineController::class, 'deleteHoliday'])->name('routines.holidays.delete');

    // Export
    Route::get('/export', [RoutineController::class, 'export'])->name('routines.export');
});