<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum IncidentSeverity: string
{
    case CRITICAL = 'critical';
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function priority(): int
    {
        return match($this) {
            self::CRITICAL => 1,
            self::HIGH => 2,
            self::MEDIUM => 3,
            self::LOW => 4,
        };
    }
}
