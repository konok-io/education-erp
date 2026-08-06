<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use App\Models\Observability\ObservabilityLogEntry;
use Spatie\DataTransferObject\DataTransferObject;

class LogEntryDTO extends DataTransferObject
{
    public string $id;
    public ?string $service_id;
    public string $level;
    public string $source;
    public string $message;
    public ?array $context;
    public ?array $extra;
    public string $environment;
    public ?string $host;
    public ?string $trace_id;
    public ?string $span_id;
    public string $logged_at;

    public static function fromModel(ObservabilityLogEntry $logEntry): self
    {
        return new self(
            id: $logEntry->id,
            service_id: $logEntry->service_id,
            level: $logEntry->level->value,
            source: $logEntry->source,
            message: $logEntry->message,
            context: $logEntry->context,
            extra: $logEntry->extra,
            environment: $logEntry->environment,
            host: $logEntry->host,
            trace_id: $logEntry->trace_id,
            span_id: $logEntry->span_id,
            logged_at: $logEntry->logged_at->toIso8601String(),
        );
    }

    public static function fromCollection(array $logEntries): array
    {
        return array_map(fn($logEntry) => self::fromModel($logEntry), $logEntries);
    }
}
