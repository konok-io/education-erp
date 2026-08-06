<?php

declare(strict_types=1);

namespace App\Enums\Transport;

enum VehicleType: string
{
    case BUS = 'bus';
    case MINIBUS = 'minibus';
    case VAN = 'van';
    case CAR = 'car';
    case MICROBUS = 'microbus';

    public function label(): string
    {
        return match($this) {
            self::BUS => 'Bus',
            self::MINIBUS => 'Minibus',
            self::VAN => 'Van',
            self::CAR => 'Car',
            self::MICROBUS => 'Microbus',
        };
    }

    public function capacity(): int
    {
        return match($this) {
            self::BUS => 50,
            self::MINIBUS => 25,
            self::VAN => 12,
            self::CAR => 5,
            self::MICROBUS => 15,
        };
    }
}
