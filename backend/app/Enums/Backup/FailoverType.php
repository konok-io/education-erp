<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum FailoverType: string
{
    case AUTOMATIC = 'automatic';
    case MANUAL = 'manual';
    case PLANNED = 'planned';
    case EMERGENCY = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::AUTOMATIC => 'Automatic Failover',
            self::MANUAL => 'Manual Failover',
            self::PLANNED => 'Planned Switchover',
            self::EMERGENCY => 'Emergency Recovery',
        };
    }
}
