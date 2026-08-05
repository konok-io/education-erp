<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('users')->middleware(['auth:sanctum'])->group(function () {
    // List users
    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users.index');

    // Search users
    Route::get('/search', [UserController::class, 'search'])
        ->middleware('permission:users.view')
        ->name('users.search');

    // Export users
    Route::get('/export', [UserController::class, 'export'])
        ->middleware('permission:users.export')
        ->name('users.export');

    // Import users
    Route::post('/import', [UserController::class, 'import'])
        ->middleware('permission:users.import')
        ->name('users.import');

    // Bulk update status
    Route::put('/bulk-status', [UserController::class, 'bulkUpdateStatus'])
        ->middleware('permission:users.edit')
        ->name('users.bulk-update-status');

    // Create user
    Route::post('/', [UserController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('users.store');

    // View user
    Route::get('/{uuid}', [UserController::class, 'show'])
        ->middleware('permission:users.view')
        ->name('users.show');

    // Update user
    Route::put('/{uuid}', [UserController::class, 'update'])
        ->middleware('permission:users.edit')
        ->name('users.update');

    // Delete user
    Route::delete('/{uuid}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('users.destroy');

    // Update avatar
    Route::post('/{uuid}/avatar', [UserController::class, 'updateAvatar'])
        ->middleware('permission:users.edit')
        ->name('users.update-avatar');

    // Change password (admin)
    Route::post('/{uuid}/password', [UserController::class, 'changePassword'])
        ->middleware('permission:users.edit')
        ->name('users.change-password');

    // Update status
    Route::post('/{uuid}/status', [UserController::class, 'updateStatus'])
        ->middleware('permission:users.edit')
        ->name('users.update-status');

    // Assign role
    Route::post('/{uuid}/role', [UserController::class, 'assignRole'])
        ->middleware('permission:users.assign.role')
        ->name('users.assign-role');

    // Get activities
    Route::get('/{uuid}/activities', [UserController::class, 'activities'])
        ->middleware('permission:users.view')
        ->name('users.activities');

    // Get login history
    Route::get('/{uuid}/login-history', [UserController::class, 'loginHistory'])
        ->middleware('permission:users.view')
        ->name('users.login-history');
});
