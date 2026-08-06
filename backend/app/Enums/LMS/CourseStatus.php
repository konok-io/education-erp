<?php

declare(strict_types=1);

namespace App\Enums\LMS;

enum CourseStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
    case UNDER_REVISION = 'under_revision';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PENDING_REVIEW => 'Pending Review',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
            self::UNDER_REVISION => 'Under Revision',
        };
    }
}
