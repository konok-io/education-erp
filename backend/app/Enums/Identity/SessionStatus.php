<?php

declare(strict_types=1);

namespace App\Enums\Identity;

enum SessionStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case REVOKED = 'revoked';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::REVOKED => 'Revoked',
            self::EXPIRED => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'warning',
            self::REVOKED => 'error',
            self::EXPIRED => 'default',
        };
    }
}
