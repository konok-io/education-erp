<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum IncidentStatus: string
{
    case TRIGGERED = 'triggered';
    case ACKNOWLEDGED = 'acknowledged';
    case INVESTIGATING = 'investigating';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isActive(): bool
    {
        return in_array($this, [self::TRIGGERED, self::ACKNOWLEDGED, self::INVESTIGATING]);
    }

    public function isResolved(): bool
    {
        return in_array($this, [self::RESOLVED, self::CLOSED]);
    }
}
