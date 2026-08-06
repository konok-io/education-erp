<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum BackupType: string
{
    case FULL = 'full';
    case INCREMENTAL = 'incremental';
    case DIFFERENTIAL = 'differential';
    case SNAPSHOT = 'snapshot';
    case TRANSACTION_LOG = 'transaction_log';
    case IMAGE = 'image';

    public function label(): string
    {
        return match ($this) {
            self::FULL => 'Full Backup',
            self::INCREMENTAL => 'Incremental Backup',
            self::DIFFERENTIAL => 'Differential Backup',
            self::SNAPSHOT => 'Snapshot Backup',
            self::TRANSACTION_LOG => 'Transaction Log Backup',
            self::IMAGE => 'Image Backup',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FULL => 'Complete backup of all data',
            self::INCREMENTAL => 'Backup of changes since last backup',
            self::DIFFERENTIAL => 'Backup of changes since last full backup',
            self::SNAPSHOT => 'Point-in-time snapshot of data',
            self::TRANSACTION_LOG => 'Database transaction log backup',
            self::IMAGE => 'Full disk/VM image backup',
        };
    }
}
