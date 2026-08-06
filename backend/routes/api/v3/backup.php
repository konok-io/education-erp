<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V3\Backup\BackupController;
use App\Http\Controllers\Api\V3\Backup\RecoveryController;
use App\Http\Controllers\Api\V3\Backup\FailoverController;
use App\Http\Controllers\Api\V3\Backup\DRController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Backup & Disaster Recovery API Routes (v3)
|--------------------------------------------------------------------------
|
| Routes for backup, recovery, failover, and disaster recovery operations
|
*/

// DR Summary & Status
Route::prefix('dr')->group(function () {
    Route::get('/', [DRController::class, 'index']);
    Route::get('/status', [DRController::class, 'status']);
    Route::get('/summary', [DRController::class, 'summary']);
});

// Backup Jobs
Route::prefix('backups')->group(function () {
    Route::get('/', [BackupController::class, 'index']);
    Route::post('/', [BackupController::class, 'store']);
    Route::get('/summary', [BackupController::class, 'summary']);
    Route::get('/{id}', [BackupController::class, 'show']);
    Route::put('/{id}', [BackupController::class, 'update']);
    Route::delete('/{id}', [BackupController::class, 'destroy']);
    Route::post('/{id}/start', [BackupController::class, 'start']);
    Route::post('/{id}/complete', [BackupController::class, 'complete']);
    Route::post('/{id}/fail', [BackupController::class, 'fail']);
    Route::post('/{id}/cancel', [BackupController::class, 'cancel']);
    Route::post('/{id}/verify', [BackupController::class, 'markVerified']);
    Route::get('/{id}/snapshots', [BackupController::class, 'snapshots']);
});

// Recovery Jobs
Route::prefix('recoveries')->group(function () {
    Route::get('/', [RecoveryController::class, 'index']);
    Route::post('/', [RecoveryController::class, 'store']);
    Route::post('/from-snapshot', [RecoveryController::class, 'createFromSnapshot']);
    Route::get('/summary', [RecoveryController::class, 'summary']);
    Route::get('/{id}', [RecoveryController::class, 'show']);
    Route::post('/{id}/start', [RecoveryController::class, 'start']);
    Route::post('/{id}/complete', [RecoveryController::class, 'complete']);
    Route::post('/{id}/fail', [RecoveryController::class, 'fail']);
    Route::post('/{id}/verify', [RecoveryController::class, 'verify']);
    Route::post('/{id}/cancel', [RecoveryController::class, 'cancel']);
    Route::post('/{id}/log', [RecoveryController::class, 'addLog']);
    Route::delete('/{id}', [RecoveryController::class, 'destroy']);
});

// Failover Events
Route::prefix('failovers')->group(function () {
    Route::get('/', [FailoverController::class, 'index']);
    Route::post('/', [FailoverController::class, 'initiate']);
    Route::get('/summary', [FailoverController::class, 'summary']);
    Route::get('/{id}', [FailoverController::class, 'show']);
    Route::post('/{id}/start', [FailoverController::class, 'start']);
    Route::post('/{id}/complete', [FailoverController::class, 'complete']);
    Route::post('/{id}/fail', [FailoverController::class, 'fail']);
    Route::post('/{id}/rollback', [FailoverController::class, 'rollback']);
    Route::post('/{id}/cancel', [FailoverController::class, 'cancel']);
    Route::patch('/{id}/affected', [FailoverController::class, 'updateAffected']);
    Route::delete('/{id}', [FailoverController::class, 'destroy']);
});

// DR Sites
Route::prefix('dr-sites')->group(function () {
    Route::get('/', [FailoverController::class, 'drSites']);
    Route::post('/', [FailoverController::class, 'createDRSite']);
    Route::get('/{id}', [FailoverController::class, 'drSiteShow']);
    Route::put('/{id}', [FailoverController::class, 'updateDRSite']);
    Route::patch('/{id}/health', [FailoverController::class, 'updateDRSiteHealth']);
    Route::delete('/{id}', [FailoverController::class, 'deleteDRSite']);
});
