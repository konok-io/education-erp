<?php

declare(strict_types=1);

namespace App\Enums\Exam;

enum ExamStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case ONGOING = 'ongoing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SCHEDULED => 'Scheduled',
            self::ONGOING => 'Ongoing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::SCHEDULED, self::ONGOING]);
    }

    public function canEdit(): bool
    {
        return $this === self::DRAFT;
    }
}
