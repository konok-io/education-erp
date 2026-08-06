<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum BackupStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case PAUSED = 'paused';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RUNNING => 'Running',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::PAUSED => 'Paused',
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
            self::PAUSED => 'warning',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED, self::CANCELLED]);
    }
}
