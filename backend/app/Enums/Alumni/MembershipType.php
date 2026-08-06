<?php

declare(strict_types=1);

namespace App\Enums\Alumni;

enum MembershipType: string
{
    case LIFETIME = 'lifetime';
    case ANNUAL = 'annual';
    case MONTHLY = 'monthly';
    case FREE = 'free';
    case HONORARY = 'honorary';

    public function label(): string
    {
        return match($this) {
            self::LIFETIME => 'Lifetime',
            self::ANNUAL => 'Annual',
            self::MONTHLY => 'Monthly',
            self::FREE => 'Free',
            self::HONORARY => 'Honorary',
        };
    }
}
