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

enum ContentType: string
{
    case VIDEO = 'video';
    case PDF = 'pdf';
    case DOCUMENT = 'document';
    case IMAGE = 'image';
    case AUDIO = 'audio';
    case LINK = 'link';
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case LIVE_CLASS = 'live_class';

    public function label(): string
    {
        return match($this) {
            self::VIDEO => 'Video',
            self::PDF => 'PDF',
            self::DOCUMENT => 'Document',
            self::IMAGE => 'Image',
            self::AUDIO => 'Audio',
            self::LINK => 'External Link',
            self::QUIZ => 'Quiz',
            self::ASSIGNMENT => 'Assignment',
            self::LIVE_CLASS => 'Live Class',
        };
    }
}
