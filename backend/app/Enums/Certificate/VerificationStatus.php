<?php

declare(strict_types=1);

namespace App\Enums\Certificate;

enum VerificationStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case INVALID = 'invalid';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::VERIFIED => 'Verified',
            self::INVALID => 'Invalid',
            self::EXPIRED => 'Expired',
        };
    }
}
