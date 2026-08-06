<?php

declare(strict_types=1);

namespace App\Services\Observability;

use App\DTO\Observability\DashboardSummaryDTO;
use App\Enums\Observability\AlertSeverity;
use App\Enums\Observability\IncidentSeverity;
use App\Enums\Observability\ServiceStatus;
use App\Models\Observability\ObservabilityAlert;
use App\Models\Observability\ObservabilityIncident;
use App\Models\Observability\ObservabilityService as ObservabilityServiceModel;
use App\Models\Observability\ObservabilitySlo;

class DashboardService
{
    public function getSummary(string $environment = 'production'): DashboardSummaryDTO
    {
        $services = ObservabilityServiceModel::active()
            ->byEnvironment($environment)
            ->get();

        $activeAlerts = ObservabilityAlert::active()
            ->byEnvironment($environment)
            ->get();

        $activeIncidents = ObservabilityIncident::active()
            ->byEnvironment($environment)
            ->get();

        $slos = ObservabilitySlo::active()
            ->byEnvironment($environment)
            ->get();

        $recentIncidents = ObservabilityIncident::byEnvironment($environment)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($incident) => [
                'id' => $incident->id,
                'incident_number' => $incident->incident_number,
                'title' => $incident->title,
                'severity' => $incident->severity->value,
                'status' => $incident->status->value,
                'started_at' => $incident->started_at->toIso8601String(),
            ])
            ->toArray();

        $topAlerts = $activeAlerts
            ->sortBy(fn($alert) => $alert->severity->priority())
            ->take(5)
            ->map(fn($alert) => [
                'id' => $alert->id,
                'name' => $alert->name,
                'severity' => $alert->severity->value,
                'status' => $alert->status->value,
                'triggered_at' => $alert->triggered_at?->toIso8601String(),
                'service_id' => $alert->service_id,
            ])
            ->toArray();

        $sloSummary = $slos->map(fn($slo) => [
            'id' => $slo->id,
            'name' => $slo->name,
            'type' => $slo->type->value,
            'target' => (float) $slo->target_percentage,
            'current' => $slo->current_percentage ? (float) $slo->current_percentage : null,
            'is_breached' => $slo->isBreached(),
        ])->toArray();

        return DashboardSummaryDTO::fromArray([
            'total_services' => $services->count(),
            'healthy_services' => $services->where('status', ServiceStatus::HEALTHY)->count(),
            'degraded_services' => $services->where('status', ServiceStatus::DEGRADED)->count(),
            'down_services' => $services->where('status', ServiceStatus::DOWN)->count(),
            'active_alerts' => $activeAlerts->count(),
            'active_incidents' => $activeIncidents->count(),
            'critical_incidents' => $activeIncidents->where('severity', IncidentSeverity::CRITICAL)->count(),
            'average_availability' => $this->calculateAverageAvailability($slos),
            'alert_severity_breakdown' => [
                'critical' => $activeAlerts->where('severity', AlertSeverity::CRITICAL)->count(),
                'high' => $activeAlerts->where('severity', AlertSeverity::HIGH)->count(),
                'medium' => $activeAlerts->where('severity', AlertSeverity::MEDIUM)->count(),
                'low' => $activeAlerts->where('severity', AlertSeverity::LOW)->count(),
                'info' => $activeAlerts->where('severity', AlertSeverity::INFO)->count(),
            ],
            'incident_severity_breakdown' => [
                'critical' => $activeIncidents->where('severity', IncidentSeverity::CRITICAL)->count(),
                'high' => $activeIncidents->where('severity', IncidentSeverity::HIGH)->count(),
                'medium' => $activeIncidents->where('severity', IncidentSeverity::MEDIUM)->count(),
                'low' => $activeIncidents->where('severity', IncidentSeverity::LOW)->count(),
            ],
            'recent_incidents' => $recentIncidents,
            'top_alerts' => $topAlerts,
            'slo_summary' => $sloSummary,
        ]);
    }

    protected function calculateAverageAvailability(iterable $slos): float
    {
        $slosArray = collect($slos)->filter(fn($slo) => $slo->current_percentage !== null);

        if ($slosArray->isEmpty()) {
            return 100.0;
        }

        return round($slosArray->avg('current_percentage'), 2);
    }

    public function getServiceHealthTrends(string $environment = 'production', int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $alerts = ObservabilityAlert::byEnvironment($environment)
            ->between($startDate, now())
            ->get()
            ->groupBy(fn($alert) => $alert->triggered_at?->format('Y-m-d') ?? 'unknown');

        $incidents = ObservabilityIncident::byEnvironment($environment)
            ->between($startDate, now())
            ->get()
            ->groupBy(fn($incident) => $incident->started_at->format('Y-m-d'));

        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $data[] = [
                'date' => $date,
                'alerts' => $alerts[$date]?->count() ?? 0,
                'incidents' => $incidents[$date]?->count() ?? 0,
            ];
        }

        return $data;
    }

    public function getUptimeStats(string $environment = 'production'): array
    {
        $services = ObservabilityServiceModel::active()
            ->byEnvironment($environment)
            ->get();

        return $services->map(fn($service) => [
            'id' => $service->id,
            'name' => $service->name,
            'type' => $service->type->value,
            'status' => $service->status->value,
            'uptime_percentage' => $this->calculateServiceUptime($service),
        ])->toArray();
    }

    protected function calculateServiceUptime(ObservabilityServiceModel $service): float
    {
        $daysInMonth = now()->daysInMonth;
        $startOfMonth = now()->startOfMonth();

        $incidents = ObservabilityIncident::where('service_id', $service->id)
            ->where('started_at', '>=', $startOfMonth)
            ->get();

        $totalDowntimeMinutes = 0;

        foreach ($incidents as $incident) {
            $endTime = $incident->resolved_at ?? now();
            $downtimeMinutes = $incident->started_at->diffInMinutes($endTime);
            $totalDowntimeMinutes += $downtimeMinutes;
        }

        $totalMinutesInMonth = $daysInMonth * 24 * 60;
        $uptimePercentage = (($totalMinutesInMonth - $totalDowntimeMinutes) / $totalMinutesInMonth) * 100;

        return round(max(0, $uptimePercentage), 2);
    }
}
