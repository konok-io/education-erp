<?php

declare(strict_types=1);

namespace App\Enums\DataWarehouse;

enum DataStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }
}

enum SyncStatus: string
{
    case IDLE = 'idle';
    case SYNCING = 'syncing';
    case SYNCED = 'synced';
    case ERROR = 'error';

    public function label(): string
    {
        return match($this) {
            self::IDLE => 'Idle',
            self::SYNCING => 'Syncing',
            self::SYNCED => 'Synced',
            self::ERROR => 'Error',
        };
    }
}

enum SyncDirection: string
{
    case SOURCE_TO_TARGET = 'source_to_target';
    case TARGET_TO_SOURCE = 'target_to_source';
    case BIDIRECTIONAL = 'bidirectional';

    public function label(): string
    {
        return match($this) {
            self::SOURCE_TO_TARGET => 'Source to Target',
            self::TARGET_TO_SOURCE => 'Target to Source',
            self::BIDIRECTIONAL => 'Bidirectional',
        };
    }
}
