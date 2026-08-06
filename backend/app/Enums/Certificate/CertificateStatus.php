<?php

declare(strict_types=1);

namespace App\Enums\Certificate;

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
