<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V3\Observability\AlertController;
use App\Http\Controllers\Api\V3\Observability\HealthCheckController;
use App\Http\Controllers\Api\V3\Observability\IncidentController;
use App\Http\Controllers\Api\V3\Observability\MetricController;
use App\Http\Controllers\Api\V3\Observability\ObservabilityController;
use App\Http\Controllers\Api\V3\Observability\ServiceController;
use App\Http\Controllers\Api\V3\Observability\StatusPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Observability API v3 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('observability')->group(function () {

    // Dashboard & Overview
    Route::get('/dashboard', [ObservabilityController::class, 'dashboard'])->name('observability.dashboard');
    Route::get('/summary', [ObservabilityController::class, 'summary'])->name('observability.summary');
    Route::get('/health', [ObservabilityController::class, 'health'])->name('observability.health');
    Route::get('/uptime', [ObservabilityController::class, 'uptime'])->name('observability.uptime');

    // Services
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('observability.services.index');
        Route::get('/active', [ServiceController::class, 'active'])->name('observability.services.active');
        Route::get('/health', [ServiceController::class, 'health'])->name('observability.services.health');
        Route::get('/{id}', [ServiceController::class, 'show'])->name('observability.services.show');
        Route::post('/', [ServiceController::class, 'store'])->name('observability.services.store');
        Route::put('/{id}', [ServiceController::class, 'update'])->name('observability.services.update');
        Route::patch('/{id}', [ServiceController::class, 'update'])->name('observability.services.patch');
        Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('observability.services.destroy');
        Route::post('/{id}/toggle', [ServiceController::class, 'toggle'])->name('observability.services.toggle');
    });

    // Metrics
    Route::prefix('metrics')->group(function () {
        Route::get('/', [MetricController::class, 'index'])->name('observability.metrics.index');
        Route::get('/latest/{serviceId}', [MetricController::class, 'latest'])->name('observability.metrics.latest');
        Route::get('/timeseries/{serviceId}', [MetricController::class, 'timeSeries'])->name('observability.metrics.timeseries');
        Route::get('/aggregates/{serviceId}', [MetricController::class, 'aggregates'])->name('observability.metrics.aggregates');
        Route::post('/', [MetricController::class, 'store'])->name('observability.metrics.store');
        Route::post('/cleanup', [MetricController::class, 'cleanup'])->name('observability.metrics.cleanup');
    });

    // Alerts
    Route::prefix('alerts')->group(function () {
        Route::get('/', [AlertController::class, 'index'])->name('observability.alerts.index');
        Route::get('/active', [AlertController::class, 'active'])->name('observability.alerts.active');
        Route::get('/summary', [AlertController::class, 'summary'])->name('observability.alerts.summary');
        Route::get('/{id}', [AlertController::class, 'show'])->name('observability.alerts.show');
        Route::post('/', [AlertController::class, 'store'])->name('observability.alerts.store');
        Route::put('/{id}', [AlertController::class, 'update'])->name('observability.alerts.update');
        Route::patch('/{id}', [AlertController::class, 'update'])->name('observability.alerts.patch');
        Route::post('/{id}/trigger', [AlertController::class, 'trigger'])->name('observability.alerts.trigger');
        Route::post('/{id}/acknowledge', [AlertController::class, 'acknowledge'])->name('observability.alerts.acknowledge');
        Route::post('/{id}/resolve', [AlertController::class, 'resolve'])->name('observability.alerts.resolve');
        Route::post('/{id}/silence', [AlertController::class, 'silence'])->name('observability.alerts.silence');
        Route::delete('/{id}', [AlertController::class, 'destroy'])->name('observability.alerts.destroy');
    });

    // Incidents
    Route::prefix('incidents')->group(function () {
        Route::get('/', [IncidentController::class, 'index'])->name('observability.incidents.index');
        Route::get('/active', [IncidentController::class, 'active'])->name('observability.incidents.active');
        Route::get('/summary', [IncidentController::class, 'summary'])->name('observability.incidents.summary');
        Route::get('/number/{incidentNumber}', [IncidentController::class, 'showByNumber'])->name('observability.incidents.number');
        Route::get('/{id}', [IncidentController::class, 'show'])->name('observability.incidents.show');
        Route::post('/', [IncidentController::class, 'store'])->name('observability.incidents.store');
        Route::post('/from-alert/{alertId}', [IncidentController::class, 'createFromAlert'])->name('observability.incidents.from-alert');
        Route::put('/{id}', [IncidentController::class, 'update'])->name('observability.incidents.update');
        Route::patch('/{id}', [IncidentController::class, 'update'])->name('observability.incidents.patch');
        Route::post('/{id}/acknowledge', [IncidentController::class, 'acknowledge'])->name('observability.incidents.acknowledge');
        Route::post('/{id}/assign', [IncidentController::class, 'assign'])->name('observability.incidents.assign');
        Route::post('/{id}/resolve', [IncidentController::class, 'resolve'])->name('observability.incidents.resolve');
        Route::post('/{id}/close', [IncidentController::class, 'close'])->name('observability.incidents.close');
        Route::post('/{id}/timeline', [IncidentController::class, 'addTimelineEvent'])->name('observability.incidents.timeline');
        Route::post('/{id}/postmortem', [IncidentController::class, 'addPostmortem'])->name('observability.incidents.postmortem');
        Route::delete('/{id}', [IncidentController::class, 'destroy'])->name('observability.incidents.destroy');
    });

    // Health Checks
    Route::prefix('health-checks')->group(function () {
        Route::get('/', [HealthCheckController::class, 'index'])->name('observability.health-checks.index');
        Route::get('/active', [HealthCheckController::class, 'active'])->name('observability.health-checks.active');
        Route::get('/summary', [HealthCheckController::class, 'summary'])->name('observability.health-checks.summary');
        Route::get('/{id}', [HealthCheckController::class, 'show'])->name('observability.health-checks.show');
        Route::post('/', [HealthCheckController::class, 'store'])->name('observability.health-checks.store');
        Route::put('/{id}', [HealthCheckController::class, 'update'])->name('observability.health-checks.update');
        Route::patch('/{id}', [HealthCheckController::class, 'update'])->name('observability.health-checks.patch');
        Route::post('/{id}/toggle', [HealthCheckController::class, 'toggle'])->name('observability.health-checks.toggle');
        Route::post('/{id}/execute', [HealthCheckController::class, 'execute'])->name('observability.health-checks.execute');
        Route::post('/execute-all', [HealthCheckController::class, 'executeAll'])->name('observability.health-checks.execute-all');
        Route::get('/{id}/results', [HealthCheckController::class, 'results'])->name('observability.health-checks.results');
        Route::delete('/{id}', [HealthCheckController::class, 'destroy'])->name('observability.health-checks.destroy');
    });

    // Status Pages
    Route::prefix('status-pages')->group(function () {
        Route::get('/', [StatusPageController::class, 'index'])->name('observability.status-pages.index');
        Route::get('/active', [StatusPageController::class, 'active'])->name('observability.status-pages.active');
        Route::get('/{id}', [StatusPageController::class, 'show'])->name('observability.status-pages.show');
        Route::get('/public/{slug}', [StatusPageController::class, 'public'])->name('observability.status-pages.public');
        Route::post('/', [StatusPageController::class, 'store'])->name('observability.status-pages.store');
        Route::put('/{id}', [StatusPageController::class, 'update'])->name('observability.status-pages.update');
        Route::patch('/{id}', [StatusPageController::class, 'update'])->name('observability.status-pages.patch');
        Route::delete('/{id}', [StatusPageController::class, 'destroy'])->name('observability.status-pages.destroy');
        Route::post('/{id}/refresh', [StatusPageController::class, 'refresh'])->name('observability.status-pages.refresh');
        
        // Components
        Route::post('/{id}/components', [StatusPageController::class, 'addComponent'])->name('observability.status-pages.components.add');
        Route::put('/components/{componentId}', [StatusPageController::class, 'updateComponent'])->name('observability.status-pages.components.update');
        Route::patch('/components/{componentId}', [StatusPageController::class, 'updateComponent'])->name('observability.status-pages.components.patch');
        Route::put('/components/{componentId}/status', [StatusPageController::class, 'updateComponentStatus'])->name('observability.status-pages.components.status');
        Route::patch('/components/{componentId}/status', [StatusPageController::class, 'updateComponentStatus'])->name('observability.status-pages.components.status.patch');
        Route::delete('/components/{componentId}', [StatusPageController::class, 'deleteComponent'])->name('observability.status-pages.components.destroy');
        
        // Incidents
        Route::post('/{id}/incidents', [StatusPageController::class, 'addIncident'])->name('observability.status-pages.incidents.add');
        Route::delete('/{id}/incidents/{incidentId}', [StatusPageController::class, 'removeIncident'])->name('observability.status-pages.incidents.remove');
    });

    // Public Status Route (for external access)
    Route::get('/status', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'operational',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    })->name('observability.public.status');
});
