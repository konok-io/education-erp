<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V3\DevSecOps\ActivityLogController;
use App\Http\Controllers\Api\V3\DevSecOps\ArtifactController;
use App\Http\Controllers\Api\V3\DevSecOps\DeploymentController;
use App\Http\Controllers\Api\V3\DevSecOps\DevSecOpsController;
use App\Http\Controllers\Api\V3\DevSecOps\EnvironmentController;
use App\Http\Controllers\Api\V3\DevSecOps\GitopsConfigController;
use App\Http\Controllers\Api\V3\DevSecOps\PipelineController;
use App\Http\Controllers\Api\V3\DevSecOps\ReleaseController;
use App\Http\Controllers\Api\V3\DevSecOps\SecurityScanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DevSecOps API v3 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('devsecops')->group(function () {
    
    // Dashboard & Overview
    Route::get('/dashboard', [DevSecOpsController::class, 'dashboard'])->name('devsecops.dashboard');
    Route::get('/summary', [DevSecOpsController::class, 'summary'])->name('devsecops.summary');
    Route::get('/health', [DevSecOpsController::class, 'health'])->name('devsecops.health');

    // Environments
    Route::prefix('environments')->group(function () {
        Route::get('/', [EnvironmentController::class, 'index'])->name('devsecops.environments.index');
        Route::get('/active', [EnvironmentController::class, 'listActive'])->name('devsecops.environments.active');
        Route::get('/{id}', [EnvironmentController::class, 'show'])->name('devsecops.environments.show');
        Route::post('/', [EnvironmentController::class, 'store'])->name('devsecops.environments.store');
        Route::put('/{id}', [EnvironmentController::class, 'update'])->name('devsecops.environments.update');
        Route::patch('/{id}', [EnvironmentController::class, 'update'])->name('devsecops.environments.patch');
        Route::delete('/{id}', [EnvironmentController::class, 'destroy'])->name('devsecops.environments.destroy');
        Route::post('/{id}/toggle', [EnvironmentController::class, 'toggleActive'])->name('devsecops.environments.toggle');
    });

    // Pipelines
    Route::prefix('pipelines')->group(function () {
        Route::get('/', [PipelineController::class, 'index'])->name('devsecops.pipelines.index');
        Route::get('/active', [PipelineController::class, 'listActive'])->name('devsecops.pipelines.active');
        Route::get('/{id}', [PipelineController::class, 'show'])->name('devsecops.pipelines.show');
        Route::post('/', [PipelineController::class, 'store'])->name('devsecops.pipelines.store');
        Route::put('/{id}', [PipelineController::class, 'update'])->name('devsecops.pipelines.update');
        Route::patch('/{id}', [PipelineController::class, 'update'])->name('devsecops.pipelines.patch');
        Route::delete('/{id}', [PipelineController::class, 'destroy'])->name('devsecops.pipelines.destroy');
        Route::post('/{id}/toggle', [PipelineController::class, 'toggleActive'])->name('devsecops.pipelines.toggle');
        Route::post('/{id}/trigger', [PipelineController::class, 'trigger'])->name('devsecops.pipelines.trigger');
        Route::get('/{id}/runs', [PipelineController::class, 'runs'])->name('devsecops.pipelines.runs');
        Route::get('/runs/{id}', [PipelineController::class, 'showRun'])->name('devsecops.pipelines.runs.show');
        Route::put('/runs/{id}/status', [PipelineController::class, 'updateRunStatus'])->name('devsecops.pipelines.runs.status');
        Route::patch('/runs/{id}/status', [PipelineController::class, 'updateRunStatus'])->name('devsecops.pipelines.runs.status.patch');
    });

    // Deployments
    Route::prefix('deployments')->group(function () {
        Route::get('/', [DeploymentController::class, 'index'])->name('devsecops.deployments.index');
        Route::get('/active', [DeploymentController::class, 'listActive'])->name('devsecops.deployments.active');
        Route::get('/{id}', [DeploymentController::class, 'show'])->name('devsecops.deployments.show');
        Route::post('/', [DeploymentController::class, 'store'])->name('devsecops.deployments.store');
        Route::put('/{id}/status', [DeploymentController::class, 'updateStatus'])->name('devsecops.deployments.status');
        Route::patch('/{id}/status', [DeploymentController::class, 'updateStatus'])->name('devsecops.deployments.status.patch');
        Route::post('/{id}/rollback', [DeploymentController::class, 'rollback'])->name('devsecops.deployments.rollback');
        Route::get('/environment/{environmentId}', [DeploymentController::class, 'byEnvironment'])->name('devsecops.deployments.environment');
        Route::get('/environment/{environmentId}/latest', [DeploymentController::class, 'latestByEnvironment'])->name('devsecops.deployments.environment.latest');
        Route::get('/environment/{environmentId}/history', [DeploymentController::class, 'history'])->name('devsecops.deployments.environment.history');
    });

    // Artifacts
    Route::prefix('artifacts')->group(function () {
        Route::get('/', [ArtifactController::class, 'index'])->name('devsecops.artifacts.index');
        Route::get('/{id}', [ArtifactController::class, 'show'])->name('devsecops.artifacts.show');
        Route::post('/', [ArtifactController::class, 'store'])->name('devsecops.artifacts.store');
        Route::put('/{id}/scan', [ArtifactController::class, 'updateScanResults'])->name('devsecops.artifacts.scan');
        Route::patch('/{id}/scan', [ArtifactController::class, 'updateScanResults'])->name('devsecops.artifacts.scan.patch');
        Route::put('/{id}/sbom', [ArtifactController::class, 'updateSbom'])->name('devsecops.artifacts.sbom');
        Route::patch('/{id}/sbom', [ArtifactController::class, 'updateSbom'])->name('devsecops.artifacts.sbom.patch');
        Route::put('/{id}/provenance', [ArtifactController::class, 'updateProvenance'])->name('devsecops.artifacts.provenance');
        Route::patch('/{id}/provenance', [ArtifactController::class, 'updateProvenance'])->name('devsecops.artifacts.provenance.patch');
        Route::post('/{id}/sign', [ArtifactController::class, 'sign'])->name('devsecops.artifacts.sign');
        Route::get('/by-type', [ArtifactController::class, 'latestByType'])->name('devsecops.artifacts.by-type');
        Route::get('/vulnerable', [ArtifactController::class, 'vulnerable'])->name('devsecops.artifacts.vulnerable');
        Route::delete('/{id}', [ArtifactController::class, 'destroy'])->name('devsecops.artifacts.destroy');
    });

    // Releases
    Route::prefix('releases')->group(function () {
        Route::get('/', [ReleaseController::class, 'index'])->name('devsecops.releases.index');
        Route::get('/published', [ReleaseController::class, 'published'])->name('devsecops.releases.published');
        Route::get('/lts', [ReleaseController::class, 'lts'])->name('devsecops.releases.lts');
        Route::get('/latest', [ReleaseController::class, 'latest'])->name('devsecops.releases.latest');
        Route::get('/{id}', [ReleaseController::class, 'show'])->name('devsecops.releases.show');
        Route::post('/', [ReleaseController::class, 'store'])->name('devsecops.releases.store');
        Route::put('/{id}', [ReleaseController::class, 'update'])->name('devsecops.releases.update');
        Route::patch('/{id}', [ReleaseController::class, 'update'])->name('devsecops.releases.patch');
        Route::post('/{id}/publish', [ReleaseController::class, 'publish'])->name('devsecops.releases.publish');
        Route::post('/{id}/deprecate', [ReleaseController::class, 'deprecate'])->name('devsecops.releases.deprecate');
        Route::delete('/{id}', [ReleaseController::class, 'destroy'])->name('devsecops.releases.destroy');
        Route::post('/bump-version', [ReleaseController::class, 'bumpVersion'])->name('devsecops.releases.bump');
    });

    // Security Scans
    Route::prefix('security')->group(function () {
        Route::get('/scans', [SecurityScanController::class, 'index'])->name('devsecops.security.scans.index');
        Route::get('/scans/{id}', [SecurityScanController::class, 'show'])->name('devsecops.security.scans.show');
        Route::post('/scans', [SecurityScanController::class, 'store'])->name('devsecops.security.scans.store');
        Route::post('/scans/{id}/start', [SecurityScanController::class, 'start'])->name('devsecops.security.scans.start');
        Route::put('/scans/{id}/results', [SecurityScanController::class, 'updateResults'])->name('devsecops.security.scans.results');
        Route::patch('/scans/{id}/results', [SecurityScanController::class, 'updateResults'])->name('devsecops.security.scans.results.patch');
        Route::post('/scans/{id}/fail', [SecurityScanController::class, 'fail'])->name('devsecops.security.scans.fail');
        Route::get('/scans/pipeline/{pipelineRunId}', [SecurityScanController::class, 'byPipelineRun'])->name('devsecops.security.scans.pipeline');
        Route::get('/scans/artifact/{artifactId}', [SecurityScanController::class, 'byArtifact'])->name('devsecops.security.scans.artifact');
        Route::get('/scans/vulnerabilities', [SecurityScanController::class, 'recentVulnerabilities'])->name('devsecops.security.scans.vulnerabilities');
        Route::get('/stats', [SecurityScanController::class, 'stats'])->name('devsecops.security.stats');
    });

    // GitOps
    Route::prefix('gitops')->group(function () {
        Route::get('/configs', [GitopsConfigController::class, 'index'])->name('devsecops.gitops.configs.index');
        Route::get('/configs/active', [GitopsConfigController::class, 'listActive'])->name('devsecops.gitops.configs.active');
        Route::get('/configs/{id}', [GitopsConfigController::class, 'show'])->name('devsecops.gitops.configs.show');
        Route::post('/configs', [GitopsConfigController::class, 'store'])->name('devsecops.gitops.configs.store');
        Route::put('/configs/{id}', [GitopsConfigController::class, 'update'])->name('devsecops.gitops.configs.update');
        Route::patch('/configs/{id}', [GitopsConfigController::class, 'update'])->name('devsecops.gitops.configs.patch');
        Route::delete('/configs/{id}', [GitopsConfigController::class, 'destroy'])->name('devsecops.gitops.configs.destroy');
        Route::post('/configs/{id}/toggle', [GitopsConfigController::class, 'toggleActive'])->name('devsecops.gitops.configs.toggle');
        Route::post('/configs/{id}/sync', [GitopsConfigController::class, 'sync'])->name('devsecops.gitops.configs.sync');
        Route::put('/configs/{id}/sync-status', [GitopsConfigController::class, 'updateSyncStatus'])->name('devsecops.gitops.configs.sync-status');
        Route::patch('/configs/{id}/sync-status', [GitopsConfigController::class, 'updateSyncStatus'])->name('devsecops.gitops.configs.sync-status.patch');
        Route::get('/configs/environment/{environmentId}', [GitopsConfigController::class, 'byEnvironment'])->name('devsecops.gitops.configs.environment');
        Route::get('/configs/provider/{provider}', [GitopsConfigController::class, 'byProvider'])->name('devsecops.gitops.configs.provider');
    });

    // Activity Logs
    Route::prefix('logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('devsecops.logs.index');
        Route::get('/recent', [ActivityLogController::class, 'recent'])->name('devsecops.logs.recent');
        Route::get('/stats', [ActivityLogController::class, 'stats'])->name('devsecops.logs.stats');
        Route::get('/pipeline-stats', [ActivityLogController::class, 'pipelineStats'])->name('devsecops.logs.pipeline-stats');
        Route::get('/deployment-stats', [ActivityLogController::class, 'deploymentStats'])->name('devsecops.logs.deployment-stats');
        Route::get('/security-stats', [ActivityLogController::class, 'securityStats'])->name('devsecops.logs.security-stats');
        Route::get('/resource/{resourceType}/{resourceId}', [ActivityLogController::class, 'byResource'])->name('devsecops.logs.resource');
    });
});
