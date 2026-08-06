<?php

declare(strict_types=1);

namespace App\Enums\Alumni;

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
