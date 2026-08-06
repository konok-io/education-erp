<?php

declare(strict_types=1);

namespace App\Enums\Hostel;

enum RoomType: string
{
    case SINGLE = 'single';
    case DOUBLE = 'double';
    case TRIPLE = 'triple';
    case DORMITORY = 'dormitory';
    case SUITE = 'suite';

    public function label(): string
    {
        return match($this) {
            self::SINGLE => 'Single Room',
            self::DOUBLE => 'Double Room',
            self::TRIPLE => 'Triple Room',
            self::DORMITORY => 'Dormitory',
            self::SUITE => 'Suite',
        };
    }

    public function capacity(): int
    {
        return match($this) {
            self::SINGLE => 1,
            self::DOUBLE => 2,
            self::TRIPLE => 3,
            self::DORMITORY => 8,
            self::SUITE => 4,
        };
    }
}

enum HostelStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case FULL = 'full';
    case UNDER_MAINTENANCE = 'under_maintenance';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::FULL => 'Full',
            self::UNDER_MAINTENANCE => 'Under Maintenance',
        };
    }
}

enum AllocationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ALLOCATED = 'allocated';
    case VACATED = 'vacated';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::ALLOCATED => 'Allocated',
            self::VACATED => 'Vacated',
        };
    }
}
