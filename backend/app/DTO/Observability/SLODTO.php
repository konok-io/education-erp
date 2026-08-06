<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use App\Models\Observability\ObservabilitySlo;
use Spatie\DataTransferObject\DataTransferObject;

class SLODTO extends DataTransferObject
{
    public string $id;
    public ?string $service_id;
    public string $name;
    public ?string $description;
    public string $type;
    public string $indicator_type;
    public string $indicator_name;
    public float $target_percentage;
    public ?float $current_percentage;
    public string $time_window;
    public string $environment;
    public ?array $threshold_config;
    public ?array $metadata;
    public bool $is_active;
    public string $created_at;
    public string $updated_at;
    public bool $is_breached;

    public static function fromModel(ObservabilitySlo $slo): self
    {
        return new self(
            id: $slo->id,
            service_id: $slo->service_id,
            name: $slo->name,
            description: $slo->description,
            type: $slo->type->value,
            indicator_type: $slo->indicator_type,
            indicator_name: $slo->indicator_name,
            target_percentage: (float) $slo->target_percentage,
            current_percentage: $slo->current_percentage ? (float) $slo->current_percentage : null,
            time_window: $slo->time_window,
            environment: $slo->environment,
            threshold_config: $slo->threshold_config,
            metadata: $slo->metadata,
            is_active: $slo->is_active,
            created_at: $slo->created_at->toIso8601String(),
            updated_at: $slo->updated_at->toIso8601String(),
            is_breached: $slo->isBreached(),
        );
    }

    public static function fromCollection(array $slos): array
    {
        return array_map(fn($slo) => self::fromModel($slo), $slos);
    }
}
