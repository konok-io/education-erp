<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum ArchiveType: string
{
    case COLD = 'cold';
    case WARM = 'warm';
    case HOT = 'hot';

    public function label(): string
    {
        return match ($this) {
            self::COLD => 'Cold Archive',
            self::WARM => 'Warm Archive',
            self::HOT => 'Hot Archive',
        };
    }

    public function retrievalTime(): string
    {
        return match ($this) {
            self::COLD => 'Hours to days',
            self::WARM => 'Minutes to hours',
            self::HOT => 'Immediate',
        };
    }
}
