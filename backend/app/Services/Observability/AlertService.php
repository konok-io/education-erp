<?php

declare(strict_types=1);

namespace App\Services\Observability;

use App\Enums\Observability\AlertSeverity;
use App\Enums\Observability\AlertStatus;
use App\Models\Observability\ObservabilityAlert;
use App\Models\Observability\ObservabilityAuditLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AlertService
{
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ObservabilityAlert::with(['service', 'triggeredByUser', 'acknowledgedByUser']);

        if (isset($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        if (isset($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['environment'])) {
            $query->where('environment', $filters['environment']);
        }

        if (isset($filters['is_active'])) {
            $query->whereNull('deleted_at');
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->between($filters['start_date'], $filters['end_date']);
        }

        $query->orderByDesc('created_at');

        return $query->paginate($perPage);
    }

    public function getActiveAlerts(): Collection
    {
        return ObservabilityAlert::active()
            ->with(['service'])
            ->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low', 'info')")
            ->orderBy('triggered_at', 'desc')
            ->get();
    }

    public function getByService(string $serviceId): Collection
    {
        return ObservabilityAlert::byService($serviceId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getBySeverity(AlertSeverity $severity): Collection
    {
        return ObservabilityAlert::active()
            ->bySeverity($severity)
            ->with(['service'])
            ->get();
    }

    public function findOrFail(string $id): ObservabilityAlert
    {
        return ObservabilityAlert::with(['service', 'triggeredByUser', 'acknowledgedByUser'])
            ->findOrFail($id);
    }

    public function create(array $data): ObservabilityAlert
    {
        $alert = ObservabilityAlert::create($data);

        ObservabilityAuditLog::log(
            eventType: 'alert_created',
            severity: $alert->severity->value,
            serviceId: $alert->service_id,
            eventData: ['alert_id' => $alert->id, 'name' => $alert->name],
        );

        return $alert;
    }

    public function trigger(string $id, float $currentValue, string $userId = null): ObservabilityAlert
    {
        $alert = $this->findOrFail($id);

        $alert->update([
            'status' => AlertStatus::ACTIVE,
            'current_value' => $currentValue,
            'triggered_at' => now(),
            'triggered_by_user_id' => $userId,
        ]);

        ObservabilityAuditLog::log(
            eventType: 'alert_triggered',
            severity: $alert->severity->value,
            serviceId: $alert->service_id,
            userId: $userId,
            eventData: [
                'alert_id' => $alert->id,
                'name' => $alert->name,
                'current_value' => $currentValue,
                'threshold' => $alert->threshold,
            ],
        );

        return $alert->fresh();
    }

    public function acknowledge(string $id, string $userId): ObservabilityAlert
    {
        $alert = $this->findOrFail($id);

        $alert->update([
            'status' => AlertStatus::ACKNOWLEDGED,
            'acknowledged_by_user_id' => $userId,
        ]);

        ObservabilityAuditLog::log(
            eventType: 'alert_acknowledged',
            severity: $alert->severity->value,
            serviceId: $alert->service_id,
            userId: $userId,
            eventData: ['alert_id' => $alert->id, 'name' => $alert->name],
        );

        return $alert->fresh();
    }

    public function resolve(string $id, string $userId = null): ObservabilityAlert
    {
        $alert = $this->findOrFail($id);

        $alert->resolve();

        ObservabilityAuditLog::log(
            eventType: 'alert_resolved',
            severity: $alert->severity->value,
            serviceId: $alert->service_id,
            userId: $userId,
            eventData: ['alert_id' => $alert->id, 'name' => $alert->name],
        );

        return $alert->fresh();
    }

    public function silence(string $id): ObservabilityAlert
    {
        $alert = $this->findOrFail($id);

        $alert->update(['status' => AlertStatus::SILENCED]);

        return $alert->fresh();
    }

    public function update(string $id, array $data): ObservabilityAlert
    {
        $alert = $this->findOrFail($id);
        $alert->update($data);
        return $alert->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function getSummary(): array
    {
        $activeAlerts = $this->getActiveAlerts();

        return [
            'total_active' => $activeAlerts->count(),
            'by_severity' => [
                'critical' => $activeAlerts->where('severity', AlertSeverity::CRITICAL)->count(),
                'high' => $activeAlerts->where('severity', AlertSeverity::HIGH)->count(),
                'medium' => $activeAlerts->where('severity', AlertSeverity::MEDIUM)->count(),
                'low' => $activeAlerts->where('severity', AlertSeverity::LOW)->count(),
                'info' => $activeAlerts->where('severity', AlertSeverity::INFO)->count(),
            ],
        ];
    }

    public function checkThreshold(
        string $metricName,
        string $condition,
        float $threshold,
        float $currentValue
    ): bool {
        return match ($condition) {
            'gt' => $currentValue > $threshold,
            'lt' => $currentValue < $threshold,
            'eq' => $currentValue == $threshold,
            'gte' => $currentValue >= $threshold,
            'lte' => $currentValue <= $threshold,
            default => false,
        };
    }
}
