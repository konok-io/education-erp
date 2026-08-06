<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum DRSiteType: string
{
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case DR = 'dr';
    case HOT = 'hot';
    case WARM = 'warm';
    case COLD = 'cold';

    public function label(): string
    {
        return match ($this) {
            self::PRIMARY => 'Primary Site',
            self::SECONDARY => 'Secondary Site',
            self::DR => 'Disaster Recovery Site',
            self::HOT => 'Hot Standby',
            self::WARM => 'Warm Standby',
            self::COLD => 'Cold Standby',
        };
    }
}
