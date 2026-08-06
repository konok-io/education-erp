<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Observability;

use App\Http\Controllers\Controller;
use App\Services\Observability\DashboardService;
use App\Services\Observability\ObservabilityService as ObservabilityServiceModel;
use App\Services\Observability\AlertService;
use App\Services\Observability\IncidentService;
use App\Services\Observability\HealthCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObservabilityController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected ObservabilityServiceModel $observabilityService,
        protected AlertService $alertService,
        protected IncidentService $incidentService,
        protected HealthCheckService $healthCheckService
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');

        $summary = $this->dashboardService->getSummary($environment);
        $serviceHealth = $this->observabilityService->getHealthSummary();
        $alertSummary = $this->alertService->getSummary();
        $incidentSummary = $this->incidentService->getSummary();
        $healthSummary = $this->healthCheckService->getSummary();

        return $this->successResponse([
            'summary' => $summary,
            'service_health' => $serviceHealth,
            'alert_summary' => $alertSummary,
            'incident_summary' => $incidentSummary,
            'health_summary' => $healthSummary,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');

        $summary = $this->dashboardService->getSummary($environment);

        return $this->successResponse($summary);
    }

    public function health(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');

        $services = $this->observabilityService->getAllActive();
        $healthChecks = $this->healthCheckService->getActiveChecks();

        $overallHealth = 'healthy';

        if ($services->where('status', 'down')->isNotEmpty()) {
            $overallHealth = 'down';
        } elseif ($services->where('status', 'degraded')->isNotEmpty()) {
            $overallHealth = 'degraded';
        } elseif ($healthChecks->where('status', 'unhealthy')->isNotEmpty()) {
            $overallHealth = 'degraded';
        }

        return $this->successResponse([
            'status' => $overallHealth,
            'services_count' => $services->count(),
            'healthy_services' => $services->where('status', 'healthy')->count(),
            'degraded_services' => $services->where('status', 'degraded')->count(),
            'down_services' => $services->where('status', 'down')->count(),
            'health_checks_count' => $healthChecks->count(),
            'healthy_checks' => $healthChecks->where('status', 'healthy')->count(),
            'unhealthy_checks' => $healthChecks->where('status', 'unhealthy')->count(),
        ]);
    }

    public function uptime(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');
        $days = (int) $request->input('days', 30);

        $uptimeStats = $this->dashboardService->getUptimeStats($environment);
        $trends = $this->dashboardService->getServiceHealthTrends($environment, $days);

        return $this->successResponse([
            'uptime' => $uptimeStats,
            'trends' => $trends,
        ]);
    }

    protected function successResponse(array $data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
