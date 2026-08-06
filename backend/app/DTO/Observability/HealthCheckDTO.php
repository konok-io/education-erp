<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use App\Models\Observability\ObservabilityHealthCheck;
use Spatie\DataTransferObject\DataTransferObject;

class HealthCheckDTO extends DataTransferObject
{
    public string $id;
    public ?string $service_id;
    public string $name;
    public string $type;
    public ?string $endpoint;
    public string $method;
    public string $status;
    public int $check_interval_seconds;
    public int $timeout_seconds;
    public int $retry_count;
    public ?array $headers;
    public ?array $expected_response;
    public bool $is_active;
    public string $environment;
    public ?array $metadata;
    public ?string $last_check_at;
    public ?string $last_status;
    public ?string $last_error;
    public ?float $last_response_time_ms;
    public string $created_at;
    public string $updated_at;

    public static function fromModel(ObservabilityHealthCheck $healthCheck): self
    {
        return new self(
            id: $healthCheck->id,
            service_id: $healthCheck->service_id,
            name: $healthCheck->name,
            type: $healthCheck->type->value,
            endpoint: $healthCheck->endpoint,
            method: $healthCheck->method,
            status: $healthCheck->status->value,
            check_interval_seconds: $healthCheck->check_interval_seconds,
            timeout_seconds: $healthCheck->timeout_seconds,
            retry_count: $healthCheck->retry_count,
            headers: $healthCheck->headers,
            expected_response: $healthCheck->expected_response,
            is_active: $healthCheck->is_active,
            environment: $healthCheck->environment,
            metadata: $healthCheck->metadata,
            last_check_at: $healthCheck->last_check_at?->toIso8601String(),
            last_status: $healthCheck->last_status,
            last_error: $healthCheck->last_error,
            last_response_time_ms: $healthCheck->last_response_time_ms ? (float) $healthCheck->last_response_time_ms : null,
            created_at: $healthCheck->created_at->toIso8601String(),
            updated_at: $healthCheck->updated_at->toIso8601String(),
        );
    }

    public static function fromCollection(array $healthChecks): array
    {
        return array_map(fn($healthCheck) => self::fromModel($healthCheck), $healthChecks);
    }
}
