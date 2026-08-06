<?php

declare(strict_types=1);

namespace App\Enums\Certificate;

enum CertificateType: string
{
    case TRANSCRIPT = 'transcript';
    case TESTIMONIAL = 'testimonial';
    case TRANSFER_CERTIFICATE = 'transfer_certificate';
    case CHARACTER_CERTIFICATE = 'character_certificate';
    case PROVISIONAL_CERTIFICATE = 'provisional_certificate';
    case GRADUATION_CERTIFICATE = 'graduation_certificate';
    case BONAFIDE = 'bonafide';
    case COURSE_COMPLETION = 'course_completion';
    case ACHIEVEMENT = 'achievement';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::TRANSCRIPT => 'Transcript',
            self::TESTIMONIAL => 'Testimonial',
            self::TRANSFER_CERTIFICATE => 'Transfer Certificate',
            self::CHARACTER_CERTIFICATE => 'Character Certificate',
            self::PROVISIONAL_CERTIFICATE => 'Provisional Certificate',
            self::GRADUATION_CERTIFICATE => 'Graduation Certificate',
            self::BONAFIDE => 'Bonafide Certificate',
            self::COURSE_COMPLETION => 'Course Completion Certificate',
            self::ACHIEVEMENT => 'Achievement Certificate',
            self::OTHER => 'Other',
        };
    }
}

enum CertificateStatus: string
{
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case ISSUED = 'issued';
    case REJECTED = 'rejected';
    case REVOKED = 'revoked';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::ISSUED => 'Issued',
            self::REJECTED => 'Rejected',
            self::REVOKED => 'Revoked',
        };
    }
}

enum VerificationStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case INVALID = 'invalid';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::VERIFIED => 'Verified',
            self::INVALID => 'Invalid',
            self::EXPIRED => 'Expired',
        };
    }
}
