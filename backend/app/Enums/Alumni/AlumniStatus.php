<?php

declare(strict_types=1);

namespace App\Enums\Alumni;

enum AlumniStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case DECEASED = 'deceased';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::PENDING => 'Pending',
            self::DECEASED => 'Deceased',
        };
    }
}
