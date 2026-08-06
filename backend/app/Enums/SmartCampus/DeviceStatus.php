<?php

declare(strict_types=1);

namespace App\Enums\SmartCampus;

enum DeviceStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case MALFUNCTION = 'malfunction';
    case REPLACED = 'replaced';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::MALFUNCTION => 'Malfunction',
            self::REPLACED => 'Replaced',
        };
    }
}
