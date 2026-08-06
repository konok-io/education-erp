<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum RecoveryType: string
{
    case FULL = 'full';
    case PARTIAL = 'partial';
    case FILE = 'file';
    case DATABASE = 'database';
    case POINT_IN_TIME = 'point_in_time';
    case TABLE = 'table';

    public function label(): string
    {
        return match ($this) {
            self::FULL => 'Full Recovery',
            self::PARTIAL => 'Partial Recovery',
            self::FILE => 'File Recovery',
            self::DATABASE => 'Database Recovery',
            self::POINT_IN_TIME => 'Point-in-Time Recovery',
            self::TABLE => 'Table Recovery',
        };
    }
}
