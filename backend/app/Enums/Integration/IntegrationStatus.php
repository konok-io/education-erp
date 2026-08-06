<?php

declare(strict_types=1);

namespace App\Enums\Integration;

enum IntegrationStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ERROR = 'error';
    case PENDING = 'pending';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::ERROR => 'Error',
            self::PENDING => 'Pending',
        };
    }
}
