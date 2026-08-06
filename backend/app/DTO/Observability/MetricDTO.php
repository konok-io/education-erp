<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use App\Models\Observability\ObservabilityMetric;
use Spatie\DataTransferObject\DataTransferObject;

class MetricDTO extends DataTransferObject
{
    public string $id;
    public ?string $service_id;
    public string $name;
    public string $type;
    public float $value;
    public ?string $unit;
    public string $timestamp;
    public ?array $labels;
    public ?array $tags;
    public string $environment;

    public static function fromModel(ObservabilityMetric $metric): self
    {
        return new self(
            id: $metric->id,
            service_id: $metric->service_id,
            name: $metric->name,
            type: $metric->type->value,
            value: (float) $metric->value,
            unit: $metric->unit,
            timestamp: $metric->timestamp->toIso8601String(),
            labels: $metric->labels,
            tags: $metric->tags,
            environment: $metric->environment,
        );
    }

    public static function fromCollection(array $metrics): array
    {
        return array_map(fn($metric) => self::fromModel($metric), $metrics);
    }
}
