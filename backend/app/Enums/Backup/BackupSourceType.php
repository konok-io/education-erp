<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum BackupSourceType: string
{
    case DATABASE = 'database';
    case FILES = 'files';
    case MEDIA = 'media';
    case CONFIGURATION = 'configuration';
    case LOGS = 'logs';
    case ALL = 'all';

    public function label(): string
    {
        return match ($this) {
            self::DATABASE => 'Database',
            self::FILES => 'Files',
            self::MEDIA => 'Media',
            self::CONFIGURATION => 'Configuration',
            self::LOGS => 'Logs',
            self::ALL => 'All Data',
        };
    }
}
