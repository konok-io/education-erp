<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum ScheduleType: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case CUSTOM = 'custom';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
