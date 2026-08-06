<?php

declare(strict_types=1);

namespace App\Enums\Hostel;

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
