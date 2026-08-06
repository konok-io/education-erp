<?php

declare(strict_types=1);

namespace App\Enums\Hostel;

enum HostelStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case FULL = 'full';
    case UNDER_MAINTENANCE = 'under_maintenance';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::FULL => 'Full',
            self::UNDER_MAINTENANCE => 'Under Maintenance',
        };
    }
}
