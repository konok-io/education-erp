<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Academic\AcademicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Academic Master Data Routes
|--------------------------------------------------------------------------
*/

Route::prefix('academic')->middleware(['auth:sanctum'])->group(function () {

    // Academic Levels
    Route::get('/levels', [AcademicController::class, 'indexAcademicLevels'])->name('academic.levels.index');
    Route::post('/levels', [AcademicController::class, 'storeAcademicLevel'])->name('academic.levels.store');
    Route::get('/levels/{uuid}', [AcademicController::class, 'showAcademicLevel'])->name('academic.levels.show');
    Route::put('/levels/{uuid}', [AcademicController::class, 'updateAcademicLevel'])->name('academic.levels.update');
    Route::delete('/levels/{uuid}', [AcademicController::class, 'destroyAcademicLevel'])->name('academic.levels.destroy');

    // Faculties
    Route::get('/faculties', [AcademicController::class, 'indexFaculties'])->name('academic.faculties.index');
    Route::post('/faculties', [AcademicController::class, 'storeFaculty'])->name('academic.faculties.store');
    Route::get('/faculties/{uuid}', [AcademicController::class, 'showFaculty'])->name('academic.faculties.show');
    Route::put('/faculties/{uuid}', [AcademicController::class, 'updateFaculty'])->name('academic.faculties.update');
    Route::delete('/faculties/{uuid}', [AcademicController::class, 'destroyFaculty'])->name('academic.faculties.destroy');

    // Departments
    Route::get('/departments', [AcademicController::class, 'indexDepartments'])->name('academic.departments.index');
    Route::post('/departments', [AcademicController::class, 'storeDepartment'])->name('academic.departments.store');
    Route::get('/departments/{uuid}', [AcademicController::class, 'showDepartment'])->name('academic.departments.show');
    Route::put('/departments/{uuid}', [AcademicController::class, 'updateDepartment'])->name('academic.departments.update');
    Route::delete('/departments/{uuid}', [AcademicController::class, 'destroyDepartment'])->name('academic.departments.destroy');

    // Programs
    Route::get('/programs', [AcademicController::class, 'indexPrograms'])->name('academic.programs.index');
    Route::post('/programs', [AcademicController::class, 'storeProgram'])->name('academic.programs.store');
    Route::get('/programs/{uuid}', [AcademicController::class, 'showProgram'])->name('academic.programs.show');
    Route::put('/programs/{uuid}', [AcademicController::class, 'updateProgram'])->name('academic.programs.update');
    Route::delete('/programs/{uuid}', [AcademicController::class, 'destroyProgram'])->name('academic.programs.destroy');

    // Academic Sessions
    Route::get('/sessions', [AcademicController::class, 'indexSessions'])->name('academic.sessions.index');
    Route::post('/sessions', [AcademicController::class, 'storeSession'])->name('academic.sessions.store');
    Route::get('/sessions/{uuid}', [AcademicController::class, 'showSession'])->name('academic.sessions.show');
    Route::put('/sessions/{uuid}', [AcademicController::class, 'updateSession'])->name('academic.sessions.update');
    Route::delete('/sessions/{uuid}', [AcademicController::class, 'destroySession'])->name('academic.sessions.destroy');
    Route::post('/sessions/{uuid}/set-current', [AcademicController::class, 'setCurrentSession'])->name('academic.sessions.set-current');

    // Semesters
    Route::get('/semesters', [AcademicController::class, 'indexSemesters'])->name('academic.semesters.index');
    Route::post('/semesters', [AcademicController::class, 'storeSemester'])->name('academic.semesters.store');
    Route::get('/semesters/{uuid}', [AcademicController::class, 'showSemester'])->name('academic.semesters.show');
    Route::put('/semesters/{uuid}', [AcademicController::class, 'updateSemester'])->name('academic.semesters.update');
    Route::delete('/semesters/{uuid}', [AcademicController::class, 'destroySemester'])->name('academic.semesters.destroy');

    // Classes
    Route::get('/classes', [AcademicController::class, 'indexClasses'])->name('academic.classes.index');
    Route::post('/classes', [AcademicController::class, 'storeClass'])->name('academic.classes.store');
    Route::get('/classes/{uuid}', [AcademicController::class, 'showClass'])->name('academic.classes.show');
    Route::put('/classes/{uuid}', [AcademicController::class, 'updateClass'])->name('academic.classes.update');
    Route::delete('/classes/{uuid}', [AcademicController::class, 'destroyClass'])->name('academic.classes.destroy');

    // Sections
    Route::get('/sections', [AcademicController::class, 'indexSections'])->name('academic.sections.index');
    Route::post('/sections', [AcademicController::class, 'storeSection'])->name('academic.sections.store');
    Route::get('/sections/{uuid}', [AcademicController::class, 'showSection'])->name('academic.sections.show');
    Route::put('/sections/{uuid}', [AcademicController::class, 'updateSection'])->name('academic.sections.update');
    Route::delete('/sections/{uuid}', [AcademicController::class, 'destroySection'])->name('academic.sections.destroy');

    // Groups
    Route::get('/groups', [AcademicController::class, 'indexGroups'])->name('academic.groups.index');
    Route::post('/groups', [AcademicController::class, 'storeGroup'])->name('academic.groups.store');
    Route::get('/groups/{uuid}', [AcademicController::class, 'showGroup'])->name('academic.groups.show');
    Route::put('/groups/{uuid}', [AcademicController::class, 'updateGroup'])->name('academic.groups.update');
    Route::delete('/groups/{uuid}', [AcademicController::class, 'destroyGroup'])->name('academic.groups.destroy');

    // Subject Categories
    Route::get('/subject-categories', [AcademicController::class, 'indexSubjectCategories'])->name('academic.subject-categories.index');
    Route::post('/subject-categories', [AcademicController::class, 'storeSubjectCategory'])->name('academic.subject-categories.store');
    Route::get('/subject-categories/{uuid}', [AcademicController::class, 'showSubjectCategory'])->name('academic.subject-categories.show');
    Route::put('/subject-categories/{uuid}', [AcademicController::class, 'updateSubjectCategory'])->name('academic.subject-categories.update');
    Route::delete('/subject-categories/{uuid}', [AcademicController::class, 'destroySubjectCategory'])->name('academic.subject-categories.destroy');

    // Subjects
    Route::get('/subjects', [AcademicController::class, 'indexSubjects'])->name('academic.subjects.index');
    Route::post('/subjects', [AcademicController::class, 'storeSubject'])->name('academic.subjects.store');
    Route::get('/subjects/{uuid}', [AcademicController::class, 'showSubject'])->name('academic.subjects.show');
    Route::put('/subjects/{uuid}', [AcademicController::class, 'updateSubject'])->name('academic.subjects.update');
    Route::delete('/subjects/{uuid}', [AcademicController::class, 'destroySubject'])->name('academic.subjects.destroy');

    // Grade Rules
    Route::get('/grade-rules', [AcademicController::class, 'indexGradeRules'])->name('academic.grade-rules.index');
    Route::post('/grade-rules', [AcademicController::class, 'storeGradeRule'])->name('academic.grade-rules.store');
    Route::get('/grade-rules/{uuid}', [AcademicController::class, 'showGradeRule'])->name('academic.grade-rules.show');
    Route::put('/grade-rules/{uuid}', [AcademicController::class, 'updateGradeRule'])->name('academic.grade-rules.update');
    Route::delete('/grade-rules/{uuid}', [AcademicController::class, 'destroyGradeRule'])->name('academic.grade-rules.destroy');

    // GPA Rules
    Route::get('/gpa-rules', [AcademicController::class, 'indexGpaRules'])->name('academic.gpa-rules.index');
    Route::post('/gpa-rules', [AcademicController::class, 'storeGpaRule'])->name('academic.gpa-rules.store');
    Route::get('/gpa-rules/{uuid}', [AcademicController::class, 'showGpaRule'])->name('academic.gpa-rules.show');
    Route::put('/gpa-rules/{uuid}', [AcademicController::class, 'updateGpaRule'])->name('academic.gpa-rules.update');
    Route::delete('/gpa-rules/{uuid}', [AcademicController::class, 'destroyGpaRule'])->name('academic.gpa-rules.destroy');

    // Academic Calendar
    Route::get('/calendar', [AcademicController::class, 'indexCalendar'])->name('academic.calendar.index');
    Route::post('/calendar', [AcademicController::class, 'storeCalendar'])->name('academic.calendar.store');
    Route::get('/calendar/{uuid}', [AcademicController::class, 'showCalendar'])->name('academic.calendar.show');
    Route::put('/calendar/{uuid}', [AcademicController::class, 'updateCalendar'])->name('academic.calendar.update');
    Route::delete('/calendar/{uuid}', [AcademicController::class, 'destroyCalendar'])->name('academic.calendar.destroy');

    // Lookups
    Route::get('/hierarchy', [AcademicController::class, 'getAcademicHierarchy'])->name('academic.hierarchy');
    Route::get('/programs/{uuid}/subjects', [AcademicController::class, 'getSubjectsByProgram'])->name('academic.programs.subjects');
    Route::get('/sessions/{uuid}/classes', [AcademicController::class, 'getClassesBySession'])->name('academic.sessions.classes');
});
