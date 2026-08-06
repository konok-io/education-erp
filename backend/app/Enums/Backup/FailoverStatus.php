<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum FailoverStatus: string
{
    case INITIATED = 'initiated';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case ROLLED_BACK = 'rolled_back';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::INITIATED => 'Initiated',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::ROLLED_BACK => 'Rolled Back',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::INITIATED => 'blue',
            self::IN_PROGRESS => 'processing',
            self::COMPLETED => 'success',
            self::FAILED => 'error',
            self::ROLLED_BACK => 'warning',
            self::CANCELLED => 'default',
        };
    }
}
