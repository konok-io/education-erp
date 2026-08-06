<?php

declare(strict_types=1);

namespace App\Services\Observability;

use App\Enums\Observability\AlertSeverity;
use App\Enums\Observability\IncidentSeverity;
use App\Enums\Observability\IncidentStatus;
use App\Models\Observability\ObservabilityAuditLog;
use App\Models\Observability\ObservabilityIncident;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class IncidentService
{
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ObservabilityIncident::with(['service', 'alert', 'assignedTo', 'createdBy']);

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

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->between($filters['start_date'], $filters['end_date']);
        }

        $query->orderByDesc('created_at');

        return $query->paginate($perPage);
    }

    public function getActiveIncidents(): Collection
    {
        return ObservabilityIncident::active()
            ->with(['service', 'assignedTo'])
            ->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low')")
            ->orderBy('started_at', 'desc')
            ->get();
    }

    public function getByService(string $serviceId): Collection
    {
        return ObservabilityIncident::byService($serviceId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function findOrFail(string $id): ObservabilityIncident
    {
        return ObservabilityIncident::with([
            'service',
            'alert',
            'assignedTo',
            'createdBy',
            'timelineEvents.user',
        ])->findOrFail($id);
    }

    public function findByNumber(string $incidentNumber): ?ObservabilityIncident
    {
        return ObservabilityIncident::with(['service', 'alert', 'assignedTo'])
            ->where('incident_number', $incidentNumber)
            ->first();
    }

    public function create(array $data): ObservabilityIncident
    {
        $data['incident_number'] = ObservabilityIncident::generateIncidentNumber();
        $data['started_at'] = $data['started_at'] ?? now();

        $incident = ObservabilityIncident::create($data);

        $incident->addTimelineEvent(
            eventType: 'status_change',
            title: 'Incident created',
            userId: $data['created_by_user_id'] ?? null,
            description: $data['description'] ?? null,
        );

        ObservabilityAuditLog::log(
            eventType: 'incident_created',
            severity: $incident->severity->value,
            serviceId: $incident->service_id,
            userId: $incident->created_by_user_id,
            eventData: [
                'incident_id' => $incident->id,
                'incident_number' => $incident->incident_number,
                'title' => $incident->title,
            ],
        );

        return $incident;
    }

    public function createFromAlert(
        string $alertId,
        string $title,
        string $userId = null,
        ?string $description = null
    ): ObservabilityIncident {
        $alert = app(AlertService::class)->findOrFail($alertId);

        return $this->create([
            'title' => $title,
            'description' => $description ?? "Created from alert: {$alert->name}",
            'severity' => $alert->severity->value,
            'status' => IncidentStatus::TRIGGERED,
            'service_id' => $alert->service_id,
            'alert_id' => $alert->id,
            'environment' => $alert->environment,
            'created_by_user_id' => $userId,
        ]);
    }

    public function acknowledge(string $id, string $userId): ObservabilityIncident
    {
        $incident = $this->findOrFail($id);
        $incident->acknowledge($userId);

        ObservabilityAuditLog::log(
            eventType: 'incident_acknowledged',
            severity: $incident->severity->value,
            serviceId: $incident->service_id,
            userId: $userId,
            eventData: [
                'incident_id' => $incident->id,
                'incident_number' => $incident->incident_number,
            ],
        );

        return $incident->fresh();
    }

    public function assign(string $id, string $userId, string $assignedByUserId): ObservabilityIncident
    {
        $incident = $this->findOrFail($id);

        $incident->update(['assigned_to_user_id' => $userId]);

        $incident->addTimelineEvent(
            eventType: 'assignment',
            title: 'Incident assigned',
            userId: $assignedByUserId,
            description: "Assigned to user: {$userId}",
        );

        ObservabilityAuditLog::log(
            eventType: 'incident_assigned',
            severity: $incident->severity->value,
            serviceId: $incident->service_id,
            userId: $assignedByUserId,
            eventData: [
                'incident_id' => $incident->id,
                'incident_number' => $incident->incident_number,
                'assigned_to' => $userId,
            ],
        );

        return $incident->fresh();
    }

    public function resolve(string $id, string $userId): ObservabilityIncident
    {
        $incident = $this->findOrFail($id);
        $incident->resolve($userId);

        ObservabilityAuditLog::log(
            eventType: 'incident_resolved',
            severity: $incident->severity->value,
            serviceId: $incident->service_id,
            userId: $userId,
            eventData: [
                'incident_id' => $incident->id,
                'incident_number' => $incident->incident_number,
            ],
        );

        return $incident->fresh();
    }

    public function close(string $id, string $userId): ObservabilityIncident
    {
        $incident = $this->findOrFail($id);
        $incident->close($userId);

        ObservabilityAuditLog::log(
            eventType: 'incident_closed',
            severity: $incident->severity->value,
            serviceId: $incident->service_id,
            userId: $userId,
            eventData: [
                'incident_id' => $incident->id,
                'incident_number' => $incident->incident_number,
            ],
        );

        return $incident->fresh();
    }

    public function addTimelineEvent(
        string $id,
        string $eventType,
        string $title,
        ?string $userId = null,
        ?string $description = null,
        ?array $metadata = null
    ): ObservabilityIncident {
        $incident = $this->findOrFail($id);

        $incident->addTimelineEvent($eventType, $title, $userId, $description, $metadata);

        return $incident->fresh();
    }

    public function addPostmortem(string $id, string $postmortem, string $userId): ObservabilityIncident
    {
        $incident = $this->findOrFail($id);

        $incident->update(['postmortem' => $postmortem]);

        $incident->addTimelineEvent(
            eventType: 'action',
            title: 'Postmortem added',
            userId: $userId,
            description: 'Postmortem document added',
        );

        return $incident->fresh();
    }

    public function update(string $id, array $data): ObservabilityIncident
    {
        $incident = $this->findOrFail($id);
        $incident->update($data);
        return $incident->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function getSummary(): array
    {
        $activeIncidents = $this->getActiveIncidents();

        return [
            'total_active' => $activeIncidents->count(),
            'by_severity' => [
                'critical' => $activeIncidents->where('severity', IncidentSeverity::CRITICAL)->count(),
                'high' => $activeIncidents->where('severity', IncidentSeverity::HIGH)->count(),
                'medium' => $activeIncidents->where('severity', IncidentSeverity::MEDIUM)->count(),
                'low' => $activeIncidents->where('severity', IncidentSeverity::LOW)->count(),
            ],
            'mean_time_to_resolution' => $this->calculateMTTR(),
        ];
    }

    public function calculateMTTR(): ?float
    {
        $resolvedIncidents = ObservabilityIncident::whereNotNull('resolved_at')
            ->whereNotNull('started_at')
            ->get();

        if ($resolvedIncidents->isEmpty()) {
            return null;
        }

        $totalDuration = $resolvedIncidents->sum(function ($incident) {
            return $incident->resolved_at->diffInSeconds($incident->started_at);
        });

        return round($totalDuration / $resolvedIncidents->count() / 60, 2);
    }
}
