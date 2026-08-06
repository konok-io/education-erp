<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use App\Models\Observability\ObservabilityIncident;
use Spatie\DataTransferObject\DataTransferObject;

class IncidentDTO extends DataTransferObject
{
    public string $id;
    public string $incident_number;
    public string $title;
    public ?string $description;
    public string $severity;
    public string $status;
    public ?string $service_id;
    public ?string $alert_id;
    public string $environment;
    public ?array $affected_components;
    public ?array $impact;
    public string $started_at;
    public ?string $acknowledged_at;
    public ?string $resolved_at;
    public ?string $closed_at;
    public ?string $assigned_to_user_id;
    public ?string $created_by_user_id;
    public ?string $postmortem;
    public ?array $timeline;
    public ?array $metadata;
    public string $created_at;
    public string $updated_at;

    public static function fromModel(ObservabilityIncident $incident): self
    {
        return new self(
            id: $incident->id,
            incident_number: $incident->incident_number,
            title: $incident->title,
            description: $incident->description,
            severity: $incident->severity->value,
            status: $incident->status->value,
            service_id: $incident->service_id,
            alert_id: $incident->alert_id,
            environment: $incident->environment,
            affected_components: $incident->affected_components,
            impact: $incident->impact,
            started_at: $incident->started_at->toIso8601String(),
            acknowledged_at: $incident->acknowledged_at?->toIso8601String(),
            resolved_at: $incident->resolved_at?->toIso8601String(),
            closed_at: $incident->closed_at?->toIso8601String(),
            assigned_to_user_id: $incident->assigned_to_user_id,
            created_by_user_id: $incident->created_by_user_id,
            postmortem: $incident->postmortem,
            timeline: $incident->timeline,
            metadata: $incident->metadata,
            created_at: $incident->created_at->toIso8601String(),
            updated_at: $incident->updated_at->toIso8601String(),
        );
    }

    public static function fromCollection(array $incidents): array
    {
        return array_map(fn($incident) => self::fromModel($incident), $incidents);
    }
}
