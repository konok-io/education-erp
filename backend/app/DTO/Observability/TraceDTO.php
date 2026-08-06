<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use App\Models\Observability\ObservabilityTrace;
use Spatie\DataTransferObject\DataTransferObject;

class TraceDTO extends DataTransferObject
{
    public string $id;
    public ?string $service_id;
    public string $trace_id;
    public string $name;
    public string $operation;
    public string $status;
    public float $duration_ms;
    public string $environment;
    public string $started_at;
    public string $ended_at;
    public ?array $tags;
    public string $created_at;
    public string $updated_at;

    public static function fromModel(ObservabilityTrace $trace): self
    {
        return new self(
            id: $trace->id,
            service_id: $trace->service_id,
            trace_id: $trace->trace_id,
            name: $trace->name,
            operation: $trace->operation,
            status: $trace->status,
            duration_ms: (float) $trace->duration_ms,
            environment: $trace->environment,
            started_at: $trace->started_at->toIso8601String(),
            ended_at: $trace->ended_at->toIso8601String(),
            tags: $trace->tags,
            created_at: $trace->created_at->toIso8601String(),
            updated_at: $trace->updated_at->toIso8601String(),
        );
    }

    public static function fromCollection(array $traces): array
    {
        return array_map(fn($trace) => self::fromModel($trace), $traces);
    }
}
