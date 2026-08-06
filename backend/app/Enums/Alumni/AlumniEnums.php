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

enum DonationType: string
{
    case ONE_TIME = 'one_time';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';
    case ENDOWMENT = 'endowment';

    public function label(): string
    {
        return match($this) {
            self::ONE_TIME => 'One Time',
            self::MONTHLY => 'Monthly',
            self::QUARTERLY => 'Quarterly',
            self::YEARLY => 'Yearly',
            self::ENDOWMENT => 'Endowment',
        };
    }
}
