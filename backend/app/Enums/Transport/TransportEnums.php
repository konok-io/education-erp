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

enum RouteStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
        };
    }
}

enum TransportStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::ACTIVE => 'Active',
            self::CANCELLED => 'Cancelled',
        };
    }
}
