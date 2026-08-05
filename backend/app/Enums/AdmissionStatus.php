<?php

declare(strict_types=1);

namespace App\Enums;

enum AdmissionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ALUMNI = 'alumni';
    case TRANSFERRED = 'transferred';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::ALUMNI => 'Alumni',
            self::TRANSFERRED => 'Transferred',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::ACTIVE => 'success',
            self::INACTIVE => 'secondary',
            self::ALUMNI => 'primary',
            self::TRANSFERRED => 'info',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}