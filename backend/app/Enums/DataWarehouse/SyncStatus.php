<?php

declare(strict_types=1);

namespace App\Enums\DataWarehouse;

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
