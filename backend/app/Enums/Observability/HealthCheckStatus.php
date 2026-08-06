<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum HealthCheckStatus: string
{
    case HEALTHY = 'healthy';
    case DEGRADED = 'degraded';
    case UNHEALTHY = 'unhealthy';
    case UNKNOWN = 'unknown';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isHealthy(): bool
    {
        return $this === self::HEALTHY;
    }

    public function isUnhealthy(): bool
    {
        return $this === self::UNHEALTHY;
    }
}
