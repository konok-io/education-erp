<?php

declare(strict_types=1);

namespace App\Enums\Library;

enum BookStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case BORROWED = 'borrowed';
    case UNDER_MAINTENANCE = 'under_maintenance';
    case LOST = 'lost';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Available',
            self::RESERVED => 'Reserved',
            self::BORROWED => 'Borrowed',
            self::UNDER_MAINTENANCE => 'Under Maintenance',
            self::LOST => 'Lost',
            self::ARCHIVED => 'Archived',
        };
    }
}
