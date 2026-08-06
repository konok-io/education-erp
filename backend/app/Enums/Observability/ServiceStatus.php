<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum ServiceStatus: string
{
    case HEALTHY = 'healthy';
    case DEGRADED = 'degraded';
    case DOWN = 'down';
    case UNKNOWN = 'unknown';
    case MAINTENANCE = 'maintenance';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isHealthy(): bool
    {
        return $this === self::HEALTHY;
    }

    public function isDegraded(): bool
    {
        return $this === self::DEGRADED;
    }

    public function isDown(): bool
    {
        return $this === self::DOWN;
    }
}
