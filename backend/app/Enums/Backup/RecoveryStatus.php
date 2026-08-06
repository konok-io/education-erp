<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum RecoveryStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case VERIFIED = 'verified';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RUNNING => 'Running',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::VERIFIED => 'Verified',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'default',
            self::RUNNING => 'processing',
            self::COMPLETED => 'success',
            self::FAILED => 'error',
            self::CANCELLED => 'warning',
            self::VERIFIED => 'success',
        };
    }
}
