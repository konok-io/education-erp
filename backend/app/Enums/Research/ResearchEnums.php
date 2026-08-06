<?php

declare(strict_types=1);

namespace App\Enums\Research;

enum ResearchStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case REVISION_REQUESTED = 'revision_requested';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PUBLISHED = 'published';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::UNDER_REVIEW => 'Under Review',
            self::REVISION_REQUESTED => 'Revision Requested',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::PUBLISHED => 'Published',
        };
    }
}

enum PublicationStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case PUBLISHED = 'published';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Accepted',
            self::PUBLISHED => 'Published',
            self::REJECTED => 'Rejected',
            self::WITHDRAWN => 'Withdrawn',
        };
    }
}

enum OBEStatus: string
{
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::ACTIVE => 'Active',
            self::ARCHIVED => 'Archived',
        };
    }
}
