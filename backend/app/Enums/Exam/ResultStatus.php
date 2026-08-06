<?php

declare(strict_types=1);

namespace App\Enums\Exam;

enum ResultStatus: string
{
    case PENDING = 'pending';
    case EVALUATED = 'evaluated';
    case VERIFIED = 'verified';
    case APPROVED = 'approved';
    case PUBLISHED = 'published';
    case LOCKED = 'locked';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::EVALUATED => 'Evaluated',
            self::VERIFIED => 'Verified',
            self::APPROVED => 'Approved',
            self::PUBLISHED => 'Published',
            self::LOCKED => 'Locked',
            self::REJECTED => 'Rejected',
        };
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::PENDING, self::EVALUATED, self::VERIFIED]);
    }

    public function isLocked(): bool
    {
        return $this === self::LOCKED;
    }
}
