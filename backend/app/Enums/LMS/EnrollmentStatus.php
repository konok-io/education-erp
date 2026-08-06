<?php

declare(strict_types=1);

namespace App\Enums\LMS;

enum EnrollmentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case DROPPED = 'dropped';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::DROPPED => 'Dropped',
            self::SUSPENDED => 'Suspended',
        };
    }
}
