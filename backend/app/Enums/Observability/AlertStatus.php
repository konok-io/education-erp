<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum AlertStatus: string
{
    case ACTIVE = 'active';
    case ACKNOWLEDGED = 'acknowledged';
    case RESOLVED = 'resolved';
    case SILENCED = 'silenced';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
