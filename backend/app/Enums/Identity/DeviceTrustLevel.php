<?php

declare(strict_types=1);

namespace App\Enums\Identity;

enum DeviceTrustLevel: string
{
    case TRUSTED = 'trusted';
    case UNTRUSTED = 'untrusted';
    case VERIFIED = 'verified';

    public function label(): string
    {
        return match ($this) {
            self::TRUSTED => 'Trusted',
            self::UNTRUSTED => 'Untrusted',
            self::VERIFIED => 'Verified',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TRUSTED => 'success',
            self::UNTRUSTED => 'warning',
            self::VERIFIED => 'processing',
        };
    }
}
