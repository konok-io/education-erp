<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use App\Models\Observability\ObservabilityAlert;
use Spatie\DataTransferObject\DataTransferObject;

class AlertDTO extends DataTransferObject
{
    public string $id;
    public ?string $service_id;
    public string $name;
    public ?string $description;
    public string $severity;
    public string $status;
    public ?string $metric_name;
    public ?string $condition;
    public ?float $threshold;
    public ?float $current_value;
    public string $environment;
    public ?array $metadata;
    public ?array $labels;
    public ?string $triggered_at;
    public ?string $resolved_at;
    public ?string $triggered_by_user_id;
    public ?string $acknowledged_by_user_id;
    public string $created_at;
    public string $updated_at;

    public static function fromModel(ObservabilityAlert $alert): self
    {
        return new self(
            id: $alert->id,
            service_id: $alert->service_id,
            name: $alert->name,
            description: $alert->description,
            severity: $alert->severity->value,
            status: $alert->status->value,
            metric_name: $alert->metric_name,
            condition: $alert->condition,
            threshold: $alert->threshold ? (float) $alert->threshold : null,
            current_value: $alert->current_value ? (float) $alert->current_value : null,
            environment: $alert->environment,
            metadata: $alert->metadata,
            labels: $alert->labels,
            triggered_at: $alert->triggered_at?->toIso8601String(),
            resolved_at: $alert->resolved_at?->toIso8601String(),
            triggered_by_user_id: $alert->triggered_by_user_id,
            acknowledged_by_user_id: $alert->acknowledged_by_user_id,
            created_at: $alert->created_at->toIso8601String(),
            updated_at: $alert->updated_at->toIso8601String(),
        );
    }

    public static function fromCollection(array $alerts): array
    {
        return array_map(fn($alert) => self::fromModel($alert), $alerts);
    }
}
