<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum PipelineRunStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case BLOCKED = 'blocked';
    case TIMEOUT = 'timeout';
    case SKIPPED = 'skipped';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::RUNNING => 'Running',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::BLOCKED => 'Blocked',
            self::TIMEOUT => 'Timeout',
            self::SKIPPED => 'Skipped',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::RUNNING => 'blue',
            self::SUCCESS => 'green',
            self::FAILED => 'red',
            self::CANCELLED => 'orange',
            self::BLOCKED => 'purple',
            self::TIMEOUT => 'red',
            self::SKIPPED => 'gray',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::SUCCESS,
            self::FAILED,
            self::CANCELLED,
            self::TIMEOUT,
            self::SKIPPED,
        ]);
    }
}
