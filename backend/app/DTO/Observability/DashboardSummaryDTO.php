<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use Spatie\DataTransferObject\DataTransferObject;

class DashboardSummaryDTO extends DataTransferObject
{
    public int $total_services;
    public int $healthy_services;
    public int $degraded_services;
    public int $down_services;
    public int $active_alerts;
    public int $active_incidents;
    public int $critical_incidents;
    public float $average_availability;
    public array $alert_severity_breakdown;
    public array $incident_severity_breakdown;
    public array $recent_incidents;
    public array $top_alerts;
    public array $slo_summary;

    public static function fromArray(array $data): self
    {
        return new self(
            total_services: $data['total_services'] ?? 0,
            healthy_services: $data['healthy_services'] ?? 0,
            degraded_services: $data['degraded_services'] ?? 0,
            down_services: $data['down_services'] ?? 0,
            active_alerts: $data['active_alerts'] ?? 0,
            active_incidents: $data['active_incidents'] ?? 0,
            critical_incidents: $data['critical_incidents'] ?? 0,
            average_availability: $data['average_availability'] ?? 100.0,
            alert_severity_breakdown: $data['alert_severity_breakdown'] ?? [],
            incident_severity_breakdown: $data['incident_severity_breakdown'] ?? [],
            recent_incidents: $data['recent_incidents'] ?? [],
            top_alerts: $data['top_alerts'] ?? [],
            slo_summary: $data['slo_summary'] ?? [],
        );
    }
}
